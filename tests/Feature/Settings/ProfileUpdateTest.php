<?php

use App\Livewire\Settings\Profile;
use App\Models\User;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('first_name', 'Test')
        ->set('last_name', 'User')
        ->set('email', 'test@example.com')
        ->set('phone', '+48123456789')
        ->set('address_city_id', 1)
        ->set('address_street', 'Test Street')
        ->set('address_home_number', '1')
        ->set('address_apartment_number', '2')
        ->set('address_post_code', '12-345')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->first_name)->toEqual('Test');
    expect($user->last_name)->toEqual('User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->phone)->toEqual('+48123456789');
    expect($user->address_city_id)->toEqual(1);
    expect($user->address_street)->toEqual('Test Street');
    expect($user->address_home_number)->toEqual('1');
    expect($user->address_apartment_number)->toEqual('2');
    expect($user->address_post_code)->toEqual('12-345');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('first_name', $user->first_name)
        ->set('last_name', $user->last_name)
        ->set('email', $user->email)
        ->set('phone', $user->phone)
        ->set('address_city_id', $user->address_city_id)
        ->set('address_street', $user->address_street)
        ->set('address_home_number', $user->address_home_number)
        ->set('address_apartment_number', $user->address_apartment_number)
        ->set('address_post_code', $user->address_post_code)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});