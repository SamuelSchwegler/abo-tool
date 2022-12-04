<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Role::findByName('admin');
        $office = Role::findByName('office');
        $guard = 'web';

        Permission::create([
            'name' => 'manage orders',
            'guard_name' => $guard
        ])->syncRoles([$admin, $office]);

        Permission::create([
            'name' => 'manage users',
            'guard_name' => $guard
        ])->syncRoles([$admin]);

        if(Schema::hasColumn('customers', 'pickup')) {
            Schema::dropColumns('customers', ['pickup']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('name', 'manage orders')->where('guard_name', 'web')->delete();
        Permission::where('name', 'manage users')->where('guard_name', 'web')->delete();
    }
};
