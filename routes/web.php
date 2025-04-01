<?php

use App\Livewire\Settings\Appearance as SettingsAppearance;
use App\Livewire\Settings\Password as SettingsPassword;
use App\Livewire\Settings\Profile as SettingsProfile;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Index as UserIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', SettingsProfile::class)->name('settings.profile');
    Route::get('settings/password', SettingsPassword::class)->name('settings.password');
    Route::get('settings/appearance', SettingsAppearance::class)->name('settings.appearance');


    Route::redirect('users', 'users/index');

    Route::get('/users/index', UserIndex::class)->name('users.index');
    Route::get('/users/create', UsersCreate::class)->name('users.create');
    

});

require __DIR__.'/auth.php';
