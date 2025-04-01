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
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});