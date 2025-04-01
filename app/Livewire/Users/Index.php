<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $sortField = 'id';
    public string $sortDirection = 'asc';
    public int $perPage = 25;
    public array $roles = [];
    public array $headers = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 25],
    ];

    public function mount()
    {
        $this->roles = Role::orderBy('id','asc')->pluck('name')->toArray();
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
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->with('roles') // Pobierz role użytkowników
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                // Filtrowanie po roli
                $query->whereHas('roles', function ($q) {
                    $q->roles->where('name', '=', $this->roleFilter);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.users.index', compact('users'));
    }
}
