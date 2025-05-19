<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\Device;
use App\Models\User;
use App\Models\Calendar;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Create extends Component
{
    use WithFileUploads;

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

    public $schedule_automatically = false;
    public $technicians = [];
    public $devices = [];
    public $clients = [];

    public $service_date_formatted;

    public function mount()
    {
        if (!auth()->user()->can('tickets.create')) {
            session()->flash('error', 'Nie masz uprawnień do tworzenia zgłoszeń serwisowych.');
            return redirect()->route('services.index');
        }

        $now = now();
        $this->service_date = $now->format('Y-m-d\TH:i');
        $this->service_date_formatted = $now->format('Y-m-d\TH:i');

        $this->loadTechnicians();
        $this->loadClients();

        if (!empty($this->client_id)) {
            $this->loadDevices();
        }

        if (count($this->technicians) > 0) {
            $this->user_id = $this->technicians[0]->id;
        }
    }

    public function updatedServiceDate($value)
    {
        try {
            $this->service_date_formatted = Carbon::parse($value)->format('Y-m-d\TH:i');
        } catch (\Exception $e) {
            $this->service_date_formatted = $value;
        }
    }

    public function loadTechnicians()
    {
        $this->technicians = User::role('Technical')->get();
    }

    public function loadDevices()
    {
        $user = Auth::user();

        if ($user->hasRole('Client')) {
            $this->devices = Device::where('client_id', $user->id)->get();
        } else {
            if ($this->client_id) {
                $this->devices = Device::where('client_id', $this->client_id)->get();
            } else {
                $this->devices = [];
            }
        }
    }

    public function loadClients()
    {
        $user = Auth::user();

        if ($user->hasRole('Client')) {
            $this->clients = collect([$user]);
            $this->client_id = $user->id;
        } else {
            $this->clients = User::role('Client')->get();
        }
    }

    public function updatedClientId()
    {
        $this->loadDevices();
    }

    public function updatedDeviceId($value)
    {
        if ($value) {
            $device = Device::find($value);
            if ($device) {
                $this->client_id = $device->client_id;
                $this->loadDevices();
            }
        }
    }

    public function save()
    {
        if (!auth()->user()->can('tickets.create')) {
            session()->flash('error', 'Nie masz uprawnień do tworzenia zgłoszeń serwisowych.');
            return redirect()->route('services.index');
        }

        $this->validate();

        $serviceDate = Carbon::parse($this->service_date)->format('Y-m-d H:i:s');

        $service = Service::create([
            'device_id' => $this->device_id,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'service_date' => $serviceDate,
            'status' => $this->status,
            'description' => $this->description,
        ]);

        if ($this->schedule_automatically) {
            $this->createCalendarEvent($service);
        }

        session()->flash('message', 'Zgłoszenie serwisowe zostało utworzone.');
        return redirect()->route('services.index');
    }

    protected function createCalendarEvent($service)
    {
        $technician = User::find($this->user_id);

        if (!$technician) {
            return;
        }

        $startTime = Carbon::parse($this->service_date);
        $endTime = $startTime->copy()->addHours(2);

        $existingEvents = Calendar::where('user_id', $technician->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $startTime)
                          ->where('end_time', '>', $endTime);
                    });
            })
            ->count();

        if ($existingEvents === 0) {
            Calendar::create([
                'user_id' => $technician->id,
                'service_id' => $service->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'planed',
            ]);

            $service->update(['status' => 'planned']);
        } else {
            $nextStart = $endTime;
            $nextEnd = $nextStart->copy()->addHours(2);

            Calendar::create([
                'user_id' => $technician->id,
                'service_id' => $service->id,
                'start_time' => $nextStart,
                'end_time' => $nextEnd,
                'status' => 'planed',
            ]);

            $service->update(['status' => 'planned']);
        }
    }

    public function cancel()
    {
        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.services.create');
    }
}
