<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\City;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(11)->create();


        $citys = [
            ['id' => 2, 'name' => 'Bydgoszcz'],
            ['id' => 3, 'name' => 'Warszawa'],
            ['id' => 4, 'name' => 'Kraków'],
            ['id' => 5, 'name' => 'Wrocław'],
            ['id' => 6, 'name' => 'Poznań'],
            ['id' => 7, 'name' => 'Gdańsk'],
            ['id' => 8, 'name' => 'Szczecin'],
            ['id' => 9, 'name' => 'Lublin'],
            ['id' => 10, 'name' => 'Białystok'],
            ['id' => 11, 'name' => 'Katowice'],
            ['id' => 12, 'name' => 'Gdynia'],
            ['id' => 13, 'name' => 'Częstochowa'],
            ['id' => 14, 'name' => 'Radom'],
            ['id' => 15, 'name' => 'Rzeszów'],
            ['id' => 16, 'name' => 'Toruń'],
            ['id' => 17, 'name' => 'Sosnowiec'],
            ['id' => 18, 'name' => 'Kielce'],
            ['id' => 19, 'name' => 'Gliwice'],
            ['id' => 20, 'name' => 'Olsztyn'],
            ['id' => 21, 'name' => 'Zielona Góra'],
        ];

        foreach ($citys as $city) {
            City::firstOrCreate(
                ['id' => $city['id']],
                ['name' => $city['name']],
            );
        }

        // Tworzenie uprawnień
        $permissions = [
            // Użytkownicy
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Klienci
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',

            // Role
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            //Uprawnienia
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Urządzenia
            'devices.create',
            'devices.view_all',
            'devices.view_own',
            'devices.manage_assigned.view',
            'devices.manage_assigned.edit',

            // Zgłoszenia
            'tickets.view_all',
            'tickets.view_assigned',
            'tickets.view_own',
            'tickets.create',
            'tickets.manage_assigned.view',
            'tickets.manage_assigned.edit',
            'tickets.report',
            'tickets.approve',
            'tickets.pending.view',

            // Faktury
            'invoices.view_all',
            'invoices.view_own',
            'invoices.view_own.download',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rola Administratora
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo($permissions);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Ad',
                'last_name' => 'Min',
                'password' => Hash::make('admin123'),
                'address_city_id' => 2,
            ]
        );
        $admin->syncRoles($adminRole);

        // Rola Techniczna
        $technicalRole = Role::firstOrCreate(['name' => 'Technical']);
        $technicalPermissions = [
            'clients.view',
            'clients.create',
            'clients.edit',

            // Urządzenia (zarządzanie przypisanymi)
            'devices.create',
            'devices.view_all',
            'devices.manage_assigned.view',
            'devices.manage_assigned.edit',

            // Zgłoszenia (zarządzanie przypisanymi)
            'tickets.view_assigned',
            'tickets.create',
            'tickets.manage_assigned.view',
            'tickets.manage_assigned.edit',
            'tickets.approve',
            'tickets.pending.view',

        ];
        $technicalRole->givePermissionTo($technicalPermissions);

        $technician = User::firstOrCreate(
            ['email' => 'technic@example.com'],
            [
                'first_name' => 'Tech',
                'last_name' => 'Nic',
                'password' => Hash::make('technic123'),
                'address_city_id' => 2,
            ]
        );
        $technician->syncRoles($technicalRole);

        // Rola Klienta
        $clientRole = Role::firstOrCreate(['name' => 'Client']);
        $clientPermissions = [
            // Urządzenia (tylko własne)
            'devices.view_own',
            'devices.create',

            // Zgłoszenia (tworzenie i przeglądanie własnych)
            'tickets.create',
            'tickets.view_own',
            'tickets.report',

            // Faktury (tylko własne)
            'invoices.view_own',
            'invoices.view_own.download',
        ];
        $clientRole->givePermissionTo($clientPermissions);

        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
                'first_name' => 'Cli',
                'last_name' => 'Ent',
                'password' => Hash::make('client123'),
                'address_city_id' => 2,
            ]
        );
        $client->syncRoles($clientRole);
    }
}
