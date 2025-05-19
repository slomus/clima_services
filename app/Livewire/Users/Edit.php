<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\City;
use Illuminate\Validation\Rule;
use Masmerise\Toaster\Toaster;

class Edit extends Component
{
    public ?User $user;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public int $address_city_id = 0;
    public string $address_street = '';
    public string $address_home_number = '';
    public string $address_apartment_number = '';
    public string $address_post_code = '';
    public array $cities = [];
    /**
     * Mount the component.
     */
    public function mount($userId): void
    {
        $this->user = User::findOrFail($userId);
        $this->first_name = $this->user->first_name ?? '';
        $this->last_name = $this->user->last_name ?? '';
        $this->email = $this->user->email ?? '';
        $this->phone = $this->user->phone ?? '';
        $this->address_city_id = $this->user->address_city_id ?? 0;
        $this->address_street = $this->user->address_street ?? '';
        $this->address_home_number = $this->user->address_home_number ?? '';
        $this->address_apartment_number = $this->user->address_apartment_number ?? '';
        $this->address_post_code = $this->user->address_post_code ?? '';
        $this->cities = City::all()->toArray();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateUserInformtion(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
            'phone' => ['nullable', 'string', 'regex:/^$|^\+48(?:\d{9}|\s\d{3}\s\d{3}\s\d{3})$/', Rule::unique(User::class)->ignore($this->user?->id)],
            'address_city_id' => ['required', 'integer'],
            'address_street' => ['required', 'string', 'max:255'],
            'address_home_number' => ['required', 'string', 'max:255'],
            'address_apartment_number' => ['nullable', 'string', 'max:255'],
            'address_post_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
        ], [
            'first_name.required' => 'Imię jest wymagane',
            'first_name.string' => 'Imię musi być tekstem',
            'first_name.max' => 'Imię nie może być dłuższe niż 255 znaków',
            'last_name.required' => 'Nazwisko jest wymagane',
            'last_name.string' => 'Nazwisko musi być tekstem',
            'last_name.max' => 'Nazwisko nie może być dłuższe niż 255 znaków',
            'email.required' => 'E-mail jest wymagany',
            'email.string' => 'E-mail musi być tekstem',
            'email.lowercase' => 'E-mail musi być małymi literami',
            'email.email' => 'E-mail musi być poprawnym adresem e-mail',
            'email.max' => 'E-mail nie może być dłuższy niż 255 znaków',
            'email.unique' => 'E-mail jest już zajęty',
            'phone.string' => 'Telefon musi być tekstem',
            'phone.regex' => 'Telefon musi być poprawnym numerem telefonu tj. +48XXXXXXXXX lub +48 XXX XXX XXX',
            'phone.unique' => 'Telefon jest już zajęty',
            'address_city_id.required' => 'Miasto jest wymagane',
            'address_city_id.integer' => 'Miasto musi być liczbą całkowitą',
            'address_street.required' => 'Ulica jest wymagana',
            'address_street.string' => 'Ulica musi być tekstem',
            'address_street.max' => 'Ulica nie może być dłuższa niż 255 znaków',
            'address_home_number.required' => 'Numer domu jest wymagany',
            'address_home_number.string' => 'Numer domu musi być tekstem',
            'address_home_number.max' => 'Numer domu nie może być dłuższy niż 255 znaków',
            'address_apartment_number.string' => 'Numer mieszkania musi być tekstem',
            'address_apartment_number.max' => 'Numer mieszkania nie może być dłuższy niż 255 znaków',
            'address_post_code.required' => 'Kod pocztowy jest wymagany',
            'address_post_code.string' => 'Kod pocztowy musi być tekstem',
            'address_post_code.regex' => 'Kod pocztowy musi być w formacie XX-XXX',
        ]);



        $this->user->fill($validated);

        if ($this->user->isDirty('email')) {
            $this->user->email_verified_at = null;
        }

        $this->user->save();

        Toaster::success('Dane zostały zaktualizowane');


        $this->dispatch('user-updated', fist_name: $this->user->first_name);
    }
}
