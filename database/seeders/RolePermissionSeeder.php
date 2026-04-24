<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions untuk aset IT
        $permissions = [
            // Asset management
            'view assets',
            'create assets',
            'edit assets',
            'delete assets',
            
            // Location management
            'view locations',
            'create locations',
            'edit locations',
            'delete locations',
            
            // Maintenance
            'view maintenances',
            'create maintenances',
            'edit maintenances',
            'complete maintenances',
            
            // Audit & Reports
            'view audits',
            'perform audits',
            'view reports',
            'export reports',
            
            // Configuration
            'view configurations',
            'edit configurations',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Buat roles
        $superAdmin = Role::create(['name' => 'super_admin']);
        $admin = Role::create(['name' => 'admin']);
        $technician = Role::create(['name' => 'technician']);
        $user = Role::create(['name' => 'user']);

        // Assign permissions ke roles
        $superAdmin->givePermissionTo(Permission::all());
        
        $admin->givePermissionTo([
            'view assets', 'create assets', 'edit assets', 'delete assets',
            'view locations', 'create locations', 'edit locations', 'delete locations',
            'view maintenances', 'create maintenances', 'edit maintenances', 'complete maintenances',
            'view audits', 'perform audits',
            'view reports', 'export reports',
            'view configurations', 'edit configurations',
        ]);
        
        $technician->givePermissionTo([
            'view assets',
            'view maintenances', 'edit maintenances', 'complete maintenances',
        ]);
        
        $user->givePermissionTo([
            'view assets',
        ]);

        // Buat user Super Admin default
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $superAdminUser->assignRole('super_admin');
    }
}