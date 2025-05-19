<?php

namespace App\Livewire\Devices;

use Livewire\Component;
use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    public $model = '';
    public $producent_number = '';
    public $serial_number = '';
    public $purchase_date = '';
    public $warranty_end_date = '';
    public $user_id = null;
    public $device_id = null;
    public $isEditing = false;

    public $showQrScanner = false;
    public $clients = [];
    public $searchClient = '';

    public function mount($deviceId = null)
    {
        try {
            if ($deviceId) {
                $this->device_id = $deviceId;
                $this->loadDevice();
                $this->isEditing = true;
            }

            if (auth()->check()) {
                if (auth()->user()->hasRole('Client')) {
                    $this->user_id = auth()->id();
                } elseif (auth()->user()->hasRole(['Admin', 'employee'])) {
                    $this->loadClients();
                }
            }
        } catch (\Exception $e) {
            Log::error('Błąd w metodzie mount DeviceForm: ' . $e->getMessage());
        }
    }

    protected function loadDevice()
    {
        try {
            $device = Device::findOrFail($this->device_id);

            if (!auth()->check() && (!auth()->user()->hasRole(['Admin', 'employee']) || $device->user_id !== auth()->id())) {
                session()->flash('error', 'Nie masz uprawnień do edytowania tego urządzenia.');
                return redirect()->route('devices.index');
            }

            $this->user_id = $device->user_id;
            $this->model = $device->model;
            $this->producent_number = $device->producent_number;
            $this->serial_number = $device->serial_number;
            $this->purchase_date = $device->purchase_date;
            $this->warranty_end_date = $device->warranty_end_date;
        } catch (\Exception $e) {
            Log::error('Błąd podczas ładowania urządzenia: ' . $e->getMessage());
            session()->flash('error', 'Nie można załadować urządzenia. Spróbuj ponownie później.');
            return redirect()->route('devices.index');
        }
    }

    public function loadClients()
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Błąd podczas ładowania klientów: ' . $e->getMessage());
            $this->clients = [];
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

    public function saveDevice()
    {
        try {
            Log::info('Próba zapisania urządzenia', [
                'user_id' => $this->user_id,
                'model' => $this->model,
                'serial_number' => $this->serial_number
            ]);

            $rules = [
                'model' => 'required|string|max:260',
                'serial_number' => 'required|string|max:260|unique:devices,serial_number' . ($this->device_id ? ',' . $this->device_id : ''),
                'producent_number' => 'nullable|string|max:260',
                'purchase_date' => 'nullable|date',
                'warranty_end_date' => 'nullable|date|after_or_equal:purchase_date',
            ];

            if (auth()->check() && auth()->user()->hasRole(['Admin', 'employee'])) {
                $rules['user_id'] = 'required|exists:users,id';
            }

            $validatedData = $this->validate($rules);
            Log::info('Dane zwalidowane pomyślnie');

            if (auth()->check() && auth()->user()->hasRole('Client') && !$this->user_id) {
                $this->user_id = auth()->id();
                Log::info('Przypisano ID użytkownika z autoryzacji', ['user_id' => $this->user_id]);
            }

            if ($this->isEditing) {
                $device = Device::find($this->device_id);
                Log::info('Edycja istniejącego urządzenia', ['device_id' => $this->device_id]);

                if (!auth()->check() && (!auth()->user()->hasRole(['Admin', 'employee']) || $device->user_id !== auth()->id())) {
                    session()->flash('error', 'Nie masz uprawnień do edytowania tego urządzenia.');
                    return;
                }
            } else {
                $device = new Device();
                Log::info('Tworzenie nowego urządzenia');
            }

            // WAŻNE: Zmiana z user_id na client_id
            $device->client_id = $this->user_id; // Dopasowanie do schematu DB
            $device->model = $this->model;
            $device->producent_number = $this->producent_number;
            $device->serial_number = $this->serial_number;
            $device->purchase_date = $this->purchase_date;
            $device->warranty_end_date = $this->warranty_end_date;

            $saved = $device->save();
            Log::info('Wynik zapisu urządzenia', ['saved' => $saved, 'device_id' => $device->id]);

            session()->flash('message', $this->isEditing ? 'Urządzenie zostało zaktualizowane.' : 'Urządzenie zostało dodane.');

            return redirect()->route('devices.index');
        } catch (\Exception $e) {
            Log::error('Błąd podczas zapisywania urządzenia: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Wystąpił błąd podczas zapisywania urządzenia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.devices.create');
    }
}
