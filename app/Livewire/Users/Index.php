<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Masmerise\Toaster\Toaster;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $sortField = 'id';
    public string $sortDirection = 'desc';
    public int $perPage = 25;
    public array $roles = [];
    public array $headers = [];
    public $userId = null;
    public string $password = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 25],
    ];

    public function mount()
    {
        $this->roles = Role::query()
            ->when(!auth()->user()->can('users.view') && auth()->user()->can('clients.view'), function($query){
                $query->where('name', '!=', 'Admin');
                $query->where('name', '!=', 'Technical');
            })
            ->orderBy('id', 'asc')->pluck('name')->toArray();
        
        $this->headers = [
            'id' => 'Id',
            'first_name' => 'Imię',
            'last_name' => 'Nazwisko',
            'email' => 'E-mail',
            'roles' => 'Rola',
            'actions' => 'Akcje',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
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

    public function confirmDelete($userId)
    {
        $this->userId = $userId;
        $this->password = '';
        $this->modal('confirm-user-account-deletion')->show();
    }
    
    public function deleteUser()
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password']
        ], [
            'password.required' => 'Należy podać hasło',
            'password.string' => 'Hasło musi być tekstem',
            'password.current_password' => 'Hasło jest niepoprawne',
        ]);

        $user = User::findOrFail($this->userId);
        $user->delete();
    
        $this->reset(['userId', 'password']);
        Toaster::success('Konto zostało usunięte');
        $this->modal('confirm-user-account-deletion')->close();
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when(!auth()->user()->can('users.view') && auth()->user()->can('clients.view'), function($query){
                $query->whereHas('roles', function($q){
                    $q->where('name', '!=', 'Admin');
                    $q->where('name', '!=', 'Technical');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', '=', $this->roleFilter);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.users.index', compact('users'));
    }
}