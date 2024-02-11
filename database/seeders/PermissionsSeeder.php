<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['browse', 'create', 'update', 'delete', 'force-delete'];
        $modules = ['users', 'sucursales', 'platillos', 'ventas', 'roles'];

        foreach ($modules as $module) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => sprintf('%s-%s', $permission, $module)]);
            }
        }
        $db_permissions = Permission::get()->pluck('name')->toArray();
        $role = Role::firstOrCreate(['name' => 'Administrador']);
        Role::firstOrCreate(['name' => 'Mesero']);
        $role->givePermissionTo($db_permissions);

        $admin = User::where('email', 'admin@donporfirio.com')->first();
        $admin->assignRole('Administrador');
    }
}
