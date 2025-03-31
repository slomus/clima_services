<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Models\User;

class Create extends Component
{
    public string $email = '';
    public Role $role;
    public string $hash = '';
    public string $link = '';
    public array $roles = [];

    public function mount()
    {
        $this->roles = Role::pluck('name')->toArray();
    }

    public function createUser()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
        ], [
            'email.required' => 'Należy podać adres e-mail',
            'email.email' => 'Podany adres nie jest e-mail`em',
            'email.unique' => 'Podany adres e-mail jest już wykorzystywany',
            'role.required' => 'Należy wskazac role użytkowika',
        ]);
    
        $this->link = route('register', ['hash' => $this->hash ]);

        $createdUser = User::create([
            'email' => $this->email,
            'hash' => $this->hash
        ]);

        $createdUser->assignRole($this->role);

        $this->reset('email', 'role');
    }
    // public function render()
    // {
    //     return view('livewire.users.create');
    // }
}
