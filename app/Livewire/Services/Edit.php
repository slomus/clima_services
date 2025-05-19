<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\Device;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Edit extends Component
{
    public $serviceId;
    public $service;

    #[Rule('required|exists:devices,id')]
    public $device_id;

    #[Rule('required|exists:users,id')]
    public $client_id;

    #[Rule('required|exists:users,id')]
    public $user_id;

    #[Rule('required|date')]
    public $service_date;

    #[Rule('required|in:reported,planned,in_progress,completed,failed')]
    public $status = 'reported';

    #[Rule('nullable|string')]
    public $description;

    public $technicians = [];
    public $devices = [];
    public $clients = [];

    public function mount($serviceId)
    {
        $this->serviceId = $serviceId;
        $this->loadService();

        $user = Auth::user();
        if (!$user->can('tickets.manage_assigned.edit') &&
            !($user->can('tickets.view_own') && $this->service->client_id === $user->id)) {
            session()->flash('error', 'Nie masz uprawnień do edycji tego zgłoszenia.');
            return redirect()->route('services');
        }

        $this->loadOptions();
        $this->populateFields();
    }

    public function loadService()
    {
        $this->service = Service::with(['device', 'client', 'user', 'calendars'])->findOrFail($this->serviceId);
    }

    public function loadOptions()
    {
        $this->technicians = User::role('Technical')->get();
        $user = Auth::user();

        if ($user->hasRole('Client')) {
            $this->devices = Device::where('client_id', $user->id)->get();
            $this->clients = collect([$user]);
        } else {
            if ($this->client_id) {
                $this->devices = Device::where('client_id', $this->client_id)->get();
            } else {
                $this->devices = Device::all();
            }
            $this->clients = User::role('Client')->get();
        }
    }

    public function populateFields()
    {
        $this->device_id = $this->service->device_id;
        $this->client_id = $this->service->client_id;
        $this->user_id = $this->service->user_id;

        try {
            $this->service_date = Carbon::parse($this->service->service_date)->format('Y-m-d\TH:i');
        } catch (\Exception $e) {
            $this->service_date = now()->format('Y-m-d\TH:i');
        }

        $this->status = $this->service->status;
        $this->description = $this->service->description;
    }

    public function updatedClientId()
    {
        if ($this->client_id) {
            $this->devices = Device::where('client_id', $this->client_id)->get();
        } else {
            $this->devices = Device::all();
        }
    }

    public function updatedDeviceId($value)
    {
        if ($value) {
            $device = Device::find($value);
            if ($device) {
                $this->client_id = $device->client_id;
                $this->updatedClientId();
            }
        }
    }

    public function save()
    {
        $user = Auth::user();

        if (!$user->can('tickets.manage_assigned.edit') &&
            !($user->can('tickets.view_own') && $this->service->client_id === $user->id)) {
            session()->flash('error', 'Nie masz uprawnień do edycji tego zgłoszenia.');
            return redirect()->route('services');
        }

        if ($user->hasRole('Client') && $this->service->client_id === $user->id) {
            $this->service->update([
                'description' => $this->description,
            ]);

            session()->flash('message', 'Zgłoszenie serwisowe zostało zaktualizowane.');
            return redirect()->route('services');
        }

        $this->validate();

        $serviceDate = Carbon::parse($this->service_date)->format('Y-m-d H:i:s');

        $this->service->update([
            'device_id' => $this->device_id,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'service_date' => $serviceDate,
            'status' => $this->status,
            'description' => $this->description,
        ]);

        if ($this->service->calendars->count() > 0) {
            $statusMap = [
                'reported' => 'planed',
                'planned' => 'planed',
                'in_progress' => 'in_progress',
                'completed' => 'completed',
                'failed' => 'cancelled'
            ];

            if (isset($statusMap[$this->status])) {
                foreach ($this->service->calendars as $calendar) {
                    $calendar->update([
                        'status' => $statusMap[$this->status],
                        'user_id' => $this->user_id
                    ]);
                }
            }
        }

        session()->flash('message', 'Zgłoszenie serwisowe zostało zaktualizowane.');
        return redirect()->route('services.index');
    }

    public function cancel()
    {
        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.edit');
    }
}
