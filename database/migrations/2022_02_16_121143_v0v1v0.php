<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('street');
            $table->string('postcode');
            $table->string('city');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable();
            $table->unsignedBigInteger('delivery_address_id')->nullable();
            $table->unsignedBigInteger('billing_address_id')->nullable();
            $table->boolean('pickup')->default(false);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->json('used_orders')->nullable()->comment('verbrauchte orders vor Systemstart');
            $table->timestamps();

            $table->foreign('delivery_address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('billing_address_id')->references('id')->on('addresses')->onDelete('set null');
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('delivery_service_id');
            $table->timestamp('date');
            $table->timestamp('deadline');
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('customer_id');
            $table->foreignId('delivery_id');
            $table->foreignId('product_id')->nullable();
            $table->boolean('canceled')->default(false);
            $table->string('depository')->nullable()->comment('Abstellort');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bundles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('product_id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('price')->comment(' / 100');
            $table->integer('deliveries')->default(0);
            $table->boolean('trial')->default(false);
            $table->string('short_description')->nullable();
            $table->timestamps();
        });

        Schema::create('buys', function (Blueprint $table) {
            $table->uuid('id');
            $table->integer('bill_number')->startingValue(2000)->autoIncrement();
            $table->foreignId('customer_id');
            $table->foreignId('bundle_id');
            $table->integer('price')->comment(' / 100');
            $table->integer('delivery_cost')->comment(' / 100');
            $table->boolean('paid')->default(false);
            $table->timestamp('issued');
            $table->timestamps();

            $table->index(['bill_number']);
            $table->dropPrimary('bill_number');
            $table->primary(['id']);
        });

        Schema::create('delivery_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('pickup')->default(false);
            $table->json('days')->comment('an welchen Wochentagen soll eine Lieferung ausgelöst werden');
            $table->smallInteger('deadline_distance')->default(2)->comment('wie viel Tage vor der Lieferung soll die Deadline enden.');
            $table->integer('delivery_cost')->default(0)->comment('faktor 100');
            $table->timestamps();
        });

        Schema::create('postcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('postcode')->unique();
            $table->foreignId('delivery_service_id');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->foreignId('address_id');
            $table->string('besr_id')->nullable();
            $table->string('iban')->nullable();
            $table->timestamps();
        });

        Schema::create('item_origins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('own')->default(true);
            $table->timestamps();
        });

        // Elemente die in einer Lieferung sind
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_origin_id');
            $table->string('name');
            $table->timestamps();
        });

        // Pivot Tabelle
        Schema::create('delivery_item', function (Blueprint $table) {
            $table->foreignId('delivery_id');
            $table->foreignId('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');

        Schema::dropIfExists('addresses');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('buys');
        Schema::dropIfExists('settings');
    }
};
