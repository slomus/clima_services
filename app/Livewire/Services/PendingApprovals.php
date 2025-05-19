<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\User;
use App\Models\Device;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PendingApprovals extends Component
{
    use WithPagination;

    public $search = '';
    public $client_id = null;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $clients = [];

    public $serviceToApprove = null;
    public $selectedTechnician = null;
    public $serviceDate = null;
    public $technicians = [];

    public $serviceToReject = null;
    public $rejectReason = '';

    public $showApproveModal = false;
    public $showRejectModal = false;

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->can('tickets.approve')) {
            return redirect()->route('services.index');
        }

        $this->loadClients();
        $this->loadTechnicians();

        $this->serviceDate = Carbon::tomorrow()->format('Y-m-d\TH:i');
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

    public function loadTechnicians()
    {
        try {
            $this->technicians = User::role('Technical')->get();
        } catch (\Exception $e) {
            \Log::error('Error loading technicians: ' . $e->getMessage());
            $this->technicians = [];
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

    public function openApproveModal($serviceId)
    {
        $this->serviceToApprove = $serviceId;
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->serviceToApprove = null;
        $this->selectedTechnician = null;
    }

    public function openRejectModal($serviceId)
    {
        $this->serviceToReject = $serviceId;
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->serviceToReject = null;
        $this->rejectReason = '';
    }

    public function approveService()
    {
        $this->validate([
            'selectedTechnician' => 'required|exists:users,id',
            'serviceDate' => 'required|date|after:now',
        ], [
            'selectedTechnician.required' => 'Wybór technika jest wymagany',
            'serviceDate.required' => 'Data serwisu jest wymagana',
            'serviceDate.after' => 'Data serwisu musi być w przyszłości',
        ]);

        try {
            $service = Service::findOrFail($this->serviceToApprove);

            $service->update([
                'status' => 'reported',
                'user_id' => $this->selectedTechnician,
                'service_date' => Carbon::parse($this->serviceDate),
            ]);

            session()->flash('message', 'Zgłoszenie serwisowe zostało zatwierdzone.');
            $this->closeApproveModal();
        } catch (\Exception $e) {
            \Log::error('Error approving service: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas zatwierdzania zgłoszenia: ' . $e->getMessage());
            $this->closeApproveModal();
        }
    }

    public function rejectService()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5',
        ], [
            'rejectReason.required' => 'Podanie powodu odrzucenia jest wymagane',
            'rejectReason.min' => 'Powód odrzucenia musi zawierać co najmniej 5 znaków',
        ]);

        try {
            $service = Service::findOrFail($this->serviceToReject);

            $service->update([
                'status' => 'rejected',
                'description' => $service->description . "\n\nOdrzucono: " . $this->rejectReason,
            ]);

            session()->flash('message', 'Zgłoszenie serwisowe zostało odrzucone.');
            $this->closeRejectModal();
        } catch (\Exception $e) {
            \Log::error('Error rejecting service: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas odrzucania zgłoszenia: ' . $e->getMessage());
            $this->closeRejectModal();
        }
    }

    public function render()
    {
        $query = Service::query()
            ->with(['device', 'client'])
            ->where('status', 'pending_approval');

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

        if (!empty($this->client_id)) {
            $query->where('client_id', $this->client_id);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $pendingServices = $query->paginate(10);

        return view('livewire.services.pending-approval', [
            'pendingServices' => $pendingServices
        ]);
    }
}
