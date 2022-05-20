<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumns('orders', ['internal_comment'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('internal_comment')->nullable()->after('depository');
            });
        }

        if(!Schema::hasColumns('customers', ['depository'])) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('depository')->nullable()->after('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumns('orders', ['internal_comment'])) {
            Schema::dropColumns('orders', ['internal_comment']);
        }

        if(Schema::hasColumns('customers', ['depository'])) {
            Schema::dropColumns('customers', ['depository']);
        }
    }
};
