<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
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

        Permission::findOrCreate('manage orders', $guard)->syncRoles([$admin, $office]);
        Permission::findOrCreate('manage users', $guard)->syncRoles([$admin]);
        Permission::findOrCreate('manage bundles', $guard)->syncRoles([$admin]);

        if (Schema::hasColumn('customers', 'pickup')) {
            Schema::dropColumns('customers', ['pickup']);
        }

        if (!Schema::hasColumns('bundles', ['title', 'order'])) {
            Schema::table('bundles', function (Blueprint $table) {
                $table->string('title')->nullable()->after('name');
                $table->smallInteger('order')->after('img_path')->default(99)->comment('Tiefere Werte kommen zuerst');
            });
        }

        if(Schema::hasColumn('bundles', 'description')) {
            Schema::dropColumns('bundles', ['description']);
        }

        if (!Schema::hasColumns('settings', ['texts'])) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('texts')->nullable();
            });

            DB::table('settings')->where('id', '=', 1)->update([
                'texts' => json_encode([
                    'home_title' => 'Gemüse-Abo: Frisches Bio-Gemüse im Abonnement',
                    'home_description' => 'Saisonales Bio-Gemüse, jede Woche frisch angeliefert – hier wählen Sie das für Sie passende Abonnement. Frisch vom Gemüsefeld in Uetendorf! Diese Gemüsesorten und Salate sind aktuell im Angebot: Lauch, Karotten, Kartoffeln…'
                ])
            ]);
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

        if (Schema::hasColumns('bundles', ['title', 'order'])) {
            Schema::dropColumns('bundles', ['title', 'order']);
        }

        if(!Schema::hasColumn('bundles', 'description')) {
            Schema::table('bundles', function (Blueprint $table) {
               $table->text('description')->after('name')->nullable();
            });
        }

        if (Schema::hasColumns('settings', ['texts'])) {
            Schema::dropColumns('settings', ['texts']);
        }
    }
};
