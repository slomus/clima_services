<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\User;
use App\Models\Device;
use App\Models\Invoices;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    // Sorting and filtering properties
    public $search = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $client_id = null;

    // Modal control
    public $deleteModalOpen = false;
    public $serviceToDelete = null;

    // Invoice modal
    public $invoiceModalOpen = false;
    public $currentInvoice = null;
    public $currentService = null;

    // Data for dropdowns
    public $clients = [];

    public function mount()
    {
        if (auth()->check() && auth()->user()->hasRole('Client')) {
            $this->client_id = auth()->id();
        } elseif (auth()->check() && (auth()->user()->can('tickets.view_all') || auth()->user()->can('tickets.view_assigned'))) {
            $this->loadClients();
        }
    }

    public function loadClients()
    {
        try {
            $query = User::role('Client');
            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            }
            $this->clients = $query->get();
        } catch (\Exception $e) {
            \Log::error('Error loading clients: ' . $e->getMessage());
            $this->clients = [];
        }
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

    public function confirmDelete($serviceId)
    {
        $this->serviceToDelete = $serviceId;
        $this->deleteModalOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->deleteModalOpen = false;
        $this->serviceToDelete = null;
    }

    public function deleteService()
    {
        try {
            if (!auth()->user()->can('tickets.delete')) {
                session()->flash('error', 'Nie masz uprawnień do usuwania zgłoszeń.');
                $this->closeDeleteModal();
                return;
            }

            $service = Service::find($this->serviceToDelete);
            if ($service) {
                if ($service->calendars) {
                    $service->calendars()->delete();
                }
                $service->delete();
                session()->flash('message', 'Zgłoszenie zostało usunięte.');
            }

            $this->closeDeleteModal();
        } catch (\Exception $e) {
            \Log::error('Błąd podczas usuwania zgłoszenia: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas usuwania zgłoszenia: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function updateServiceStatus($serviceId, $newStatus)
    {
        if (!auth()->user()->can('tickets.manage_assigned.edit')) {
            session()->flash('error', 'Nie masz uprawnień do zmiany statusu zgłoszenia.');
            return;
        }

        $service = Service::findOrFail($serviceId);
        $service->update(['status' => $newStatus]);

        if ($service->calendars && $service->calendars->count() > 0) {
            $statusMap = [
                'reported' => 'planed',
                'planned' => 'planed',
                'in_progress' => 'in_progress',
                'completed' => 'completed',
                'failed' => 'cancelled'
            ];

            if (isset($statusMap[$newStatus])) {
                foreach ($service->calendars as $calendar) {
                    $calendar->update(['status' => $statusMap[$newStatus]]);
                }
            }
        }

        session()->flash('message', 'Status zgłoszenia został zaktualizowany.');
    }

    // Simplified invoice modal functions
    public function viewInvoice($serviceId)
    {
        $service = Service::with(['invoice'])->findOrFail($serviceId);

        if (!$service->invoice) {
            session()->flash('error', 'Faktura dla tego zgłoszenia nie istnieje.');
            return;
        }

        $this->currentService = $service;
        $this->currentInvoice = $service->invoice;
        $this->invoiceModalOpen = true;
    }

    public function closeInvoiceModal()
    {
        $this->invoiceModalOpen = false;
        $this->currentInvoice = null;
        $this->currentService = null;
    }

    public function render()
    {
        $query = Service::query()
            ->with(['device', 'client', 'user'])
            ->where('status', '!=', 'pending_approval');

        // Wyszukiwanie
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('device', function ($q) {
                        $q->where('model', 'like', '%' . $this->search . '%')
                            ->orWhere('serial_number', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('client', function ($q) {
                        $q->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Filtr po statusie
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Filtr po kliencie
        if (!empty($this->client_id)) {
            $query->where('client_id', $this->client_id);
        }

        // Klient widzi tylko swoje zgłoszenia
        if (auth()->check() && auth()->user()->hasRole('Client')) {
            $query->where('client_id', auth()->id());
        }

        // Sortowanie
        $query->orderBy($this->sortField, $this->sortDirection);

        $services = $query->paginate(10);

        return view('livewire.services.index', [
            'services' => $services
        ]);
    }
}
