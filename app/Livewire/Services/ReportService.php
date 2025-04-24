<?php

namespace App\Livewire\Services;

use App\Models\Service;
use App\Models\Device;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportService extends Component
{
    public $device_id;
    public $description;
    public $devices = [];

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('Client')) {
            return redirect()->route('services.index');
        }

        $this->loadClientDevices();
    }

    public function loadClientDevices()
    {
        try {
            $this->devices = Device::where('client_id', auth()->id())->get();
        } catch (\Exception $e) {
            \Log::error('Error loading devices: ' . $e->getMessage());
            $this->devices = [];
        }
    }

    public function reportService()
    {
        $this->validate([
            'device_id' => 'required|exists:devices,id',
            'description' => 'required|string|min:10',
        ], [
            'device_id.required' => 'Wybierz urządzenie',
            'description.required' => 'Opis problemu jest wymagany',
            'description.min' => 'Opis musi zawierać co najmniej 10 znaków',
        ]);

        try {
            $device = Device::findOrFail($this->device_id);
            if ($device->client_id != auth()->id()) {
                session()->flash('error', 'Nie masz uprawnień do zgłaszania serwisu dla tego urządzenia.');
                return;
            }

            $service = Service::create([
                'client_id' => auth()->id(),
                'device_id' => $this->device_id,
                'description' => $this->description,
                'status' => 'pending_approval',
                'service_date' => Carbon::now(),
                'user_id' => null,
            ]);

            $this->reset(['device_id', 'description']);

            session()->flash('message', 'Zgłoszenie serwisowe zostało wysłane i czeka na zatwierdzenie.');
            return redirect()->route('services.index');

        } catch (\Exception $e) {
            \Log::error('Error reporting service: ' . $e->getMessage());
            session()->flash('error', 'Wystąpił błąd podczas zgłaszania serwisu: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.services.report-services');
    }
}
