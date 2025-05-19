<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Service;
use App\Models\Device;
use App\Models\Invoice;

class Index extends Component
{
    public $clientsCount;
    public $openServicesCount;
    public $devicesCount;
    public $calendarEvents = [];
    public $invoicesCount = 0;
    public $invoicesSum = 0;
    public $invoicesByStatus = [];

    public function mount()
    {
        $this->clientsCount = User::role('Client')->count();
        $this->openServicesCount = Service::whereNotIn('status', ['completed', 'failed'])->count();
        $this->devicesCount = Device::count();

        $this->loadCalendarEvents();

        $this->invoicesCount = Invoice::count();
        $this->invoicesSum = Invoice::sum('amount');
        $this->invoicesByStatus = Invoice::selectRaw('payment_status, COUNT(*) as count, SUM(amount) as sum')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');
    }

    public function loadCalendarEvents()
    {
        $this->calendarEvents = Service::with(['device', 'user'])
            ->orderBy('service_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'service_date' => $service->service_date,
                    'device' => $service->device?->model . ' (' . $service->device?->serial_number . ')',
                    'technician' => $service->user?->first_name . ' ' . $service->user?->last_name,
                    'status' => $service->status,
                    'status_label' => $this->getStatusLabel($service->status),
                ];
            })->toArray();
    }

    public function getStatusLabel($status)
    {
        return match ($status) {
            'reported' => 'Zgłoszone',
            'planned' => 'Zaplanowane',
            'in_progress' => 'W trakcie',
            'completed' => 'Zakończone',
            'failed' => 'Nieudane',
            default => ucfirst($status),
        };
    }

    public function deleteEvent($id)
    {
        Service::findOrFail($id)->delete();
        $this->loadCalendarEvents();
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
