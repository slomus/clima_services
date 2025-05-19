<?php

namespace App\Livewire\Devices;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Device;
use App\Models\User;
use Masmerise\Toaster\Toaster;

class Index extends Component
{
    use WithPagination;
    public string $search = '';
    public ?int $userFilter  = null;
    public string $sortField = 'id';
    public string $sortDirection = 'desc';
    public int $perPage = 25;
    public array $headers = [];
    public array $clients = [];
    public ?int $deviceId = null;
    public string $password = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'userFilter' => ['except' => null],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 25],
    ];

    public function mount()
    {
        $this->loadClients();
        $this->headers = [
            'id' => 'Id',
            'client' => 'Klient',
            'model' => 'Model',
            'serial_number' => 'Numer seryjny',
            'producent_number' => 'Numer producenta',
            'actions' => 'Akcje',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function loadClients()
    {
        $this->clients = User::query()
            ->when(!auth()->user()->can('devices.view_all') && auth()->user()->can('devices.view_own'), function($query){
                $query->where('id', auth()->user()->id);
            })
            ->orderBy('id', 'asc')->select('id','first_name','last_name')->get()->toArray();
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
        $this->deviceId = $deviceId;
        $this->password = '';
        $this->modal('confim-device-deletion')->show();
    }
    

    public function cancelDelete()
    {
        $this->deviceId = null;
        $this->password = '';
        $this->reset('password');
    }

    public function deleteDevice()
    {
        $this->validate([
            'password'  => ['required', 'string', 'current_password']
        ],[
            'password.required' => 'Hasło jest wymagane.',
            'password.string' => 'Hasło musi być tekstem.',
            'password.current_password' => 'Hasło jest nieprawidłowe.',
        ]);

        $device = Device::findOrFail($this->deviceId);
        $device->delete();

        $this->reset(['deviceId', 'password']);

        Toaster::success('Urządzenie zostało usunięte.');
        $this->modal('confim-device-deletion')->close();
        $this->resetPage();
    }

    public function render()
    {
        $devices = Device::query()
            ->with('client')
            ->when(!auth()->user()->can('devices.view_all') && auth()->user()->can('devices.view_own'), function($query){
                $query->where('client_id', auth()->user()->id);
            })
            ->when($this->search, function($query){
                $query->where(function($q){
                    $q->whereHas('client', function($userQuery) {
                        $userQuery->where('first_name', 'like', '%'.$this->search.'%')
                            ->orWhere('last_name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                    ->orWhere('model', 'like', '%'.$this->search.'%')
                    ->orWhere('serial_number', 'like', '%'.$this->search.'%')
                    ->orWhere('producent_number', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->sortField === 'client', function ($query) {
                $query->join('users', 'users.id', '=', 'devices.client_id')
                    ->orderBy('users.id', $this->sortDirection);
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate($this->perPage);

        return view('livewire.devices.index', compact('devices'));
    }
}
