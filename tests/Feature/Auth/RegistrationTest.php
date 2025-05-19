<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;
use App\Models\User;

test('registration screen can be rendered', function () {
    $user = User::factory()->create();
    $response = $this->get('/register/' . $user->hash);

    $response->assertStatus(200);
});

test('new users can register', function () {
    $user = User::factory()->create();
    $response = Livewire::test(Register::class, ['hash' => $user->hash])
        ->set('first_name', 'Test')
        ->set('last_name', 'User')
        ->set('email', 'test@example.com')
        ->set('phone', '+48123456789')
        ->set('address_city_id', 1)
        ->set('address_street', 'Test Street')
        ->set('address_home_number', '1')
        ->set('address_apartment_number', '2')
        ->set('address_post_code', '12-345')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});