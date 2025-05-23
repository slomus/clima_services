<?php

use App\Livewire\Dashboard\Index as Dashboard;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\Settings\Appearance as SettingsAppearance;
use App\Livewire\Settings\Password as SettingsPassword;
use App\Livewire\Settings\Profile as SettingsProfile;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Devices\Index as DevicesIndex;
use App\Livewire\Devices\Create as DevicesCreate;
use App\Livewire\Devices\Edit as DevicesEdit;
use App\Livewire\Services\Index as ServicesIndex;
use App\Livewire\Services\Create as ServicesCreate;
use App\Livewire\Services\Edit as ServicesEdit;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('settings/profile', SettingsProfile::class)->name('settings.profile');
    Route::get('settings/password', SettingsPassword::class)->name('settings.password');
    Route::get('settings/appearance', SettingsAppearance::class)->name('settings.appearance');


    Route::redirect('users', 'users/index');

    Route::get('/users/index', UsersIndex::class)->name('users.index');
    Route::get('/users/create', UsersCreate::class)->name('users.create');
    Route::get('/users/edit/{userId}', UsersEdit::class)->name('users.edit');

    Route::get('/devices/index', DevicesIndex::class)->name('devices.index');
    Route::get("/devices/create", DevicesCreate::class)->name('devices.create');
    Route::get("/devices/{deviceId}/edit", DevicesEdit::class)->name('devices.edit');

    Route::get("/services/index", ServicesIndex::class)->name('services.index');
    Route::get("/services/create", ServicesCreate::class)->name('services.create');
    Route::get("services/{serviceId}/edit", ServicesEdit::class)->name('services.edit');
    Route::get('/services/report', App\Livewire\Services\ReportService::class)->name('services.report')->middleware('role:Client');
    Route::get('/services/pending-approvals', App\Livewire\Services\PendingApprovals::class)->name('services.pending-approvals')->middleware('permission:tickets.approve');
    Route::get('/reports', ReportsIndex::class)->name('reports.index');
    Route::get('/services/invoice/{invoice}', [ServicesIndex::class, 'downloadInvoice'])->name('services.download-invoice');
});

require __DIR__ . '/auth.php';
