<?php

namespace App\Livewire\Devices;

use Livewire\Component;
use App\Models\Device;
use App\Models\User;

class Index extends Component
{
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $user_id = null;
    public $clients = [];
    public $searchClient = '';
    public $isEditing = false;
    public $showQrScanner = false;

    public $showDeleteModal = false;
    public $deviceToDelete = null;

    public $devices = null;

    public function mount()
    {
        if (auth()->check() && auth()->user()->hasRole('Client')) {
            $this->user_id = auth()->id();
        } elseif (auth()->check() && auth()->user()->hasRole(['Admin', 'employee'])) {
            $this->loadClients();
        }

        $query = Device::query();

        if (auth()->check() && !auth()->user()->hasRole(['Admin', 'employee'])) {
            $query->where('user_id', auth()->id());
        } elseif ($this->user_id) {
            $query->where('user_id', $this->user_id);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('model', 'like', '%' . $this->search . '%')
                    ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                    ->orWhere('producent_number', 'like', '%' . $this->search . '%');
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $query->with('user');
        $this->devices = $query->get();
    }

    public function loadClients()
    {
        try {
            $query = User::role('Client');

            if (!empty($this->searchClient)) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->searchClient . '%')
                        ->orWhere('last_name', 'like', '%' . $this->searchClient . '%')
                        ->orWhere('email', 'like', '%' . $this->searchClient . '%');
                });
            }

            $this->clients = $query->get();
        } catch (\Exception $e) {
            \Log::error('Błąd podczas ładowania klientów: ' . $e->getMessage());
            $this->clients = [];
        }
    }

    public function updatedSearchClient()
    {
        $this->loadClients();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete($deviceId)
    {
        $this->deviceToDelete = $deviceId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->deviceToDelete = null;
        $this->showDeleteModal = false;
    }

    public function deleteDevice()
    {
        try {
            if (!auth()->check() || !auth()->user()->hasRole(['Admin', 'employee'])) {
                session()->flash('error', 'Nie masz uprawnień do usuwania urządzeń.');
                $this->showDeleteModal = false;
                return;
            }

            $device = Device::find($this->deviceToDelete);

            if ($device) {
                $device->delete();
                session()->flash('message', 'Urządzenie zostało usunięte.');
            }

            $this->showDeleteModal = false;
            $this->deviceToDelete = null;
            return redirect()->route('devices');
        } catch (\Exception $e) {
            \Log::error('Błąd podczas usuwania urządzenia: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas usuwania urządzenia: ' . $e->getMessage());
            $this->showDeleteModal = false;
        }
    }

    public function render()
    {
        return view('livewire.devices.index');
    }
}
