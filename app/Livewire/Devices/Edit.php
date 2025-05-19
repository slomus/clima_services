<?php

namespace App\Livewire\Devices;

use Livewire\Component;
use App\Models\Device;
use App\Models\User;

class Edit extends Component
{
    public $device_id;
    public $model = '';
    public $producent_number = '';
    public $serial_number = '';
    public $purchase_date = '';
    public $warranty_end_date = '';
    public $client_id = null;

    public $showQrScanner = false;
    public $clients = [];
    public $searchClient = '';

    public function mount($deviceId)
    {
        $this->device_id = $deviceId;
        $this->loadDevice();
        $this->loadClients();
    }

    protected function loadDevice()
    {
        $device = Device::findOrFail($this->device_id);

        if (auth()->check() && !auth()->user()->hasRole(['Admin', 'employee']) && $device->client_id !== auth()->id()) {
            session()->flash('error', 'Nie masz uprawnień do edytowania tego urządzenia.');
            return redirect()->route('devices.index');
        }

        $this->client_id = $device->client_id;
        $this->model = $device->model;
        $this->producent_number = $device->producent_number;
        $this->serial_number = $device->serial_number;
        $this->purchase_date = $device->purchase_date;
        $this->warranty_end_date = $device->warranty_end_date;
    }

    public function loadClients()
    {
        if (auth()->check() && auth()->user()->hasRole(['Admin', 'employee'])) {
            $query = User::role('Client');

            if (!empty($this->searchClient)) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->searchClient . '%')
                        ->orWhere('last_name', 'like', '%' . $this->searchClient . '%')
                        ->orWhere('email', 'like', '%' . $this->searchClient . '%')
                        ->orWhere('phone', 'like', '%' . $this->searchClient . '%');
                });
            }

            $this->clients = $query->get();
        }
    }

    public function updatedSearchClient()
    {
        $this->loadClients();
    }

    public function toggleQrScanner()
    {
        $this->showQrScanner = !$this->showQrScanner;
    }

    public function handleScannedCode($code)
    {
        $this->serial_number = $code;
        $this->showQrScanner = false;
    }

    public function updateDevice()
    {
        $rules = [
            'model' => 'required|string|max:260',
            'serial_number' => 'required|string|max:260|unique:devices,serial_number,' . $this->device_id,
            'producent_number' => 'nullable|string|max:260',
            'purchase_date' => 'nullable|date',
            'warranty_end_date' => 'nullable|date|after_or_equal:purchase_date',
        ];

        if (auth()->check() && auth()->user()->hasRole(['Admin', 'employee'])) {
            $rules['client_id'] = 'required|exists:users,id';
        }

        $this->validate($rules);

        $device = Device::findOrFail($this->device_id);

        if (auth()->check() && !auth()->user()->hasRole(['Admin', 'employee']) && $device->client_id !== auth()->id()) {
            session()->flash('error', 'Nie masz uprawnień do edytowania tego urządzenia.');
            return redirect()->route('devices.index');
        }

        if (auth()->check() && auth()->user()->hasRole(['Admin', 'employee'])) {
            $device->client_id = $this->client_id;
        }

        $device->model = $this->model;
        $device->producent_number = $this->producent_number;
        $device->serial_number = $this->serial_number;
        $device->purchase_date = $this->purchase_date;
        $device->warranty_end_date = $this->warranty_end_date;

        $device->save();

        session()->flash('message', 'Urządzenie zostało zaktualizowane.');
        return redirect()->route('devices.index');
    }

    public function render()
    {
        return view('livewire.devices.edit');
    }
}
