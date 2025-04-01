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
        // User::factory(10)->create();


        $citys = [
            ['id' => 1, 'name' => 'Bydgoszcz'],
            ['id' => 2, 'name' => 'Warszawa'],
            ['id' => 3, 'name' => 'Kraków'],
            ['id' => 4, 'name' => 'Wrocław'],
            ['id' => 5, 'name' => 'Poznań'],
            ['id' => 6, 'name' => 'Gdańsk'],
            ['id' => 7, 'name' => 'Szczecin'],
            ['id' => 8, 'name' => 'Lublin'],
            ['id' => 9, 'name' => 'Białystok'],
            ['id' => 10, 'name' => 'Katowice'],
            ['id' => 11, 'name' => 'Gdynia'],
            ['id' => 12, 'name' => 'Częstochowa'],
            ['id' => 13, 'name' => 'Radom'],
            ['id' => 14, 'name' => 'Rzeszów'],
            ['id' => 15, 'name' => 'Toruń'],
            ['id' => 16, 'name' => 'Sosnowiec'],
            ['id' => 17, 'name' => 'Kielce'],
            ['id' => 18, 'name' => 'Gliwice'],
            ['id' => 19, 'name' => 'Olsztyn'],
            ['id' => 20, 'name' => 'Zielona Góra'],
        ];

        foreach ($citys as $city) {
            City::firstOrCreate(
                ['id' => $city['id']],
                ['name' => $city['name']],
            );
        }

        $permissions = [
            'manage_users',
            'manage_clients',
            'manage_roles',

            //devices
            'view_all_devices',
            'manage_devices',
            'view_assigned_devices',
            'manage_assigned_devices',
            'view_own_devices',

            //tickets
            'view_all_tickets',
            'manage_tickets',
            'view_assigned_tickets',
            'manage_assigned_tickets',
            'create_tickets',
            'view_own_tickets',

            //invoices
            'view_all_invoices',
            'manage_invoices',
            'view_onw_invoices'
        ];
    
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    
        // Tworzenie ról i przypisywanie uprawnień
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo($permissions); // Admin ma wszystkie uprawnienia
    
        $technicalRole = Role::create(['name' => 'Technical']);
        $technicalRole->givePermissionTo([
            'manage_clients',
            'view_all_devices',
            'view_assigned_devices',
            'manage_assigned_devices',
            'view_assigned_tickets',
            'manage_assigned_tickets'
        ]);
    
        $clientRole = Role::create(['name' => 'Client']);
        $clientRole->givePermissionTo([
            'view_own_devices',
            'create_tickets',
            'view_own_tickets',
            'view_onw_invoices'
        ]);
    }
}