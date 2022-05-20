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
                $table->text('internal_comment')->nullable()->after('depository');
                $table->smallInteger('discount')->default(0)->after('phone');
            });
        }

        if(!Schema::hasColumns('buys', ['discount'])) {
            Schema::table('buys', function (Blueprint $table) {
               $table->smallInteger('discount')->default(0)->after('paid');
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

        if(Schema::hasColumns('customers', ['depository', 'internal_comment', 'discount'])) {
            Schema::dropColumns('customers', ['depository', 'internal_comment', 'discount']);
        }

        if(Schema::hasColumns('buys', ['discount'])) {
            Schema::dropColumns('buys', ['discount']);
        }
    }
};
