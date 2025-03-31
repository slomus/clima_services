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

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $technicianRole = Role::firstOrCreate(['name' => 'Technician']);
        $clientRole = Role::firstOrCreate(['name' => 'Client']);

        //Zezwolenia do farktur (nie chcialo mi sie rozdzielac na admin/prac/klient)
        $pInvoiceCreate = Permission::firstOrCreate(['name' => 'invoice_create_access']);
        $pInvoiceList= Permission::firstOrCreate(['name' => 'invoice_list_access']);
        $pInvoiceView= Permission::firstOrCreate(['name' => 'invoice_view_access']);
        $pInvoiceEdit= Permission::firstOrCreate(['name' => 'invoice_edit_access']);
        $pInvoiceDelete= Permission::firstOrCreate(['name' => 'invoice_delete_access']);

        // Zezwolenia admina
        $pManageRole = Permission::firstOrCreate(['name' => 'manage_role_access']);
        $pManagePermission = Permission::firstOrCreate(['name' => 'manage_permission_access']);
        $pManageUsers = Permission::firstOrCreate(['name' => 'manage_user_access']);

        // Zezwolenia pracownika
        $pClientList = Permission::firstOrCreate(['name' => 'client_list_access']);
        $pDeviceList = Permission::firstOrCreate(['name' => 'device_list_access']);
        $pDeviceCreate = Permission::firstOrCreate(['name' => 'device_create_access']);
        $pServiceManaging = Permission::firstOrCreate(['name' => 'service_manage_access']);
        $pCalendarManaging = Permission::firstOrCreate(['name' => 'calendar_manage_access']);
        $pManageClients = Permission::firstOrCreate(['name' => 'manage_client_access']);
        $pManagePendingServices = Permission::firstOrCreate(['name' => 'manage_pending_service']);

        // Zezwolenia klienta
        $pData = Permission::firstOrCreate(['name' => 'own_data_access']);
        $pDeviceListOwn = Permission::firstOrCreate(['name' => 'own_device_list_access']);
        $pServiceHistory = Permission::firstOrCreate(['name' => 'own_service_history_access']);


        $adminRole->syncPermissions([
            $pManageRole,
            $pManagePermission,
            $pManageUsers,
            $pClientList,
            $pDeviceList,
            $pServiceManaging,
            $pCalendarManaging,
            $pData,
            $pDeviceListOwn,
            $pServiceHistory,
            $pDeviceCreate,
            $pManagePendingServices,
            $pInvoiceCreate,
            $pInvoiceList,
            $pInvoiceView,
            $pInvoiceEdit,
            $pInvoiceDelete
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Ad',
                'last_name' => 'Min',
                'password' => Hash::make('admin123'),
                'address_city_id' => 1,
            ]
        );

        $admin->syncRoles($adminRole);

        $technicianRole->syncPermissions([
            $pClientList,
            $pDeviceList,
            $pServiceManaging,
            $pCalendarManaging,
            $pManageClients,
            $pData,
            $pDeviceCreate,
            $pManagePendingServices,
            $pInvoiceCreate,
            $pInvoiceList,
            $pInvoiceView
        ]);

        $technicc = User::firstOrCreate(
            ['email' => 'technic@example.com'],
            [
                'first_name' => 'Tech',
                'last_name' => 'Nic',
                'password' => Hash::make('technic123'),
                'address_city_id' => 1,
            ]
        );

        $technicc->syncRoles($technicianRole);

        $clientRole->syncPermissions([
            $pData,
            $pDeviceListOwn,
            $pServiceHistory,
            $pInvoiceView
        ]);

        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
            'first_name' => 'Cli',
            'last_name' => 'Ent',
            'password' => Hash::make('client123'),
            'address_city_id' => 1,
            ]
        );
        $client->syncRoles($clientRole);
    }
}