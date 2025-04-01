<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $first_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';
    public string $hash = '';
    /**
     * Handle an incoming registration request.
     */
    public function register()
    {
        $user = User::where('hash', $this->hash)->first();

        if (!$user) {
            Toaster::error('Nie masz dostępu');
            return $this->redirect('/', navigate: true);
        }

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user->fill($validated)->save();

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
