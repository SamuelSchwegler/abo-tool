<?php

use App\Models\Delivery;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        // Pivot Tabelle
        if (!Schema::hasTable('delivery_product_items')) {
            Schema::create('delivery_product_items', function (Blueprint $table) {
                $table->foreignId('delivery_id')->nullable();
                $table->foreignId('product_id');
                $table->foreignId('item_id');
                $table->date('date')->nullable();
            });
        }

        foreach (Delivery::has('items')->get() as $delivery) {
            $products = DB::table('products')
                ->join('orders', 'products.id', '=', 'orders.product_id')
                ->where('orders.delivery_id', $delivery->id)
                ->select('products.*')
                ->groupBy('products.id')->get();

            $items = DB::table('delivery_item')->where('delivery_id', $delivery->id)->get();

            foreach ($products as $product) {
                foreach ($items as $item) {
                    DB::table('delivery_product_items')->insert(
                        [
                            'delivery_id' => $delivery->id,
                            'product_id' => $product->id,
                            'item_id' => $item->item_id
                        ]
                    );
                }
            }
        }

        Schema::dropIfExists('delivery_items');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('delivery_product_items', 'delivery_items');
        Schema::dropColumns('delivery_items', 'product_id');
    }
};
