<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create different roles and permissions
        $authorRole = Role::create(['name' => 'author']);
        $clientRole = Role::create(['name'=>'client']);
        $createPermission = Permission::create(['name' => 'create messages']);
        $viewPermission = Permission::create(['name' => 'view messages']);
        // Assigning the permissions to the roles
        $authorRole->givePermissionTo($createPermission,$viewPermission);
        $clientRole->givePermissionTo($viewPermission);
    }
}
