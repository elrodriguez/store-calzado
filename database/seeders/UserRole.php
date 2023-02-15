<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'admin']);

        $permissions = [];
        array_push($permissions, Permission::create(['name' => 'configuracion']));
        array_push($permissions, Permission::create(['name' => 'usuarios']));
        array_push($permissions, Permission::create(['name' => 'usuarios_nuevo']));
        array_push($permissions, Permission::create(['name' => 'usuarios_editar']));
        array_push($permissions, Permission::create(['name' => 'usuarios_eliminar']));
        array_push($permissions, Permission::create(['name' => 'usuarios_ver']));
        array_push($permissions, Permission::create(['name' => 'roles']));
        array_push($permissions, Permission::create(['name' => 'productos']));
        array_push($permissions, Permission::create(['name' => 'productos_nuevo']));
        array_push($permissions, Permission::create(['name' => 'productos_editar']));
        array_push($permissions, Permission::create(['name' => 'productos_eliminar']));
        array_push($permissions, Permission::create(['name' => 'productos_codigoqr']));
        array_push($permissions, Permission::create(['name' => 'productos_salida']));
        array_push($permissions, Permission::create(['name' => 'productos_entrada']));
        array_push($permissions, Permission::create(['name' => 'caja_chica']));
        array_push($permissions, Permission::create(['name' => 'clientes']));
        array_push($permissions, Permission::create(['name' => 'clientes_nuevo']));
        array_push($permissions, Permission::create(['name' => 'clientes_editar']));
        array_push($permissions, Permission::create(['name' => 'clientes_eliminar']));
        array_push($permissions, Permission::create(['name' => 'proveedores']));
        array_push($permissions, Permission::create(['name' => 'proveedores_nuevo']));
        array_push($permissions, Permission::create(['name' => 'proveedores_editar']));
        array_push($permissions, Permission::create(['name' => 'proveedores_eliminar']));
        array_push($permissions, Permission::create(['name' => 'punto_ventas']));

        foreach ($permissions as $permission) {
            $role->givePermissionTo($permission->name);
        }

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'local_id' => 1
        ]);

        $team = Team::create([
            'user_id' => $user->id,
            'name' => 'AdminTeams',
            'personal_team' => 1
        ]);

        User::find($user->id)->update([
            'current_team_id' => $team->id
        ]);

        $user->assignRole('admin');
    }
}
