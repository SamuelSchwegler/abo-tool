<?php

use App\Models\Customer;
use App\Models\DeliveryService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // auditing
        Schema::connection(config('audit.drivers.database.connection', config('database.default')))->create('audits', function (Blueprint $table) {
            $morphPrefix = Config::get('audit.user.morph_prefix', 'user');
            $table->bigIncrements('id');
            $table->string($morphPrefix . '_type')->nullable();
            $table->unsignedBigInteger($morphPrefix . '_id')->nullable();
            $table->string('event');
            $table->morphs('auditable');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();

            $table->index([$morphPrefix . '_id', $morphPrefix . '_type']);
        });

        if (!Schema::hasColumn('delivery_services', 'option_description')) {
            Schema::table('delivery_services', function (Blueprint $table) {
                $table->string('option_description')->after('delivery_cost')->nullable();
            });

            DB::table('delivery_services')->where('pickup', '=', 1)->update([
                'option_description' => 'Ich hole mein Gemüse im Bioladen der Gartenbauschule Hünibach ab'
            ]);
        }

        if (!Schema::hasColumn('customers', 'delivery_service_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('delivery_service_id')->nullable()->after('delivery_address_id');
            });

            $pickup = DeliveryService::where('pickup', '=', 1)->first();
            foreach(Customer::whereNull('delivery_address_id')->get() as $customer) {
                $customer->update([
                    'delivery_service_id' => $pickup->id
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('audit.drivers.database.connection', config('database.default')))->drop('audits');

        if (Schema::hasColumn('delivery_services', 'option_description')) {
            Schema::dropColumns('delivery_services', ['option_description']);
        }

        if (Schema::hasColumn('customers', 'delivery_service_id')) {
            Schema::dropColumns('customers', ['delivery_service_id']);
        }
    }
};
