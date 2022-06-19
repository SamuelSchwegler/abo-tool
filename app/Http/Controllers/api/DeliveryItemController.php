<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\ItemResource;
use App\Models\Delivery;
use App\Models\DeliveryProductItem;
use App\Models\Item;
use App\Models\ItemOrigin;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DeliveryItemController extends Controller
{
    private static function itemResourceForDate(Carbon $date): JsonResource
    {
        $items = [];
        foreach (DeliveryProductItem::whereDate('date', $date->format('Y-m-d'))->get() as $item) {
            $items[] = $item->item;
        }

        return ItemResource::collection(collect($items));
    }

    public function addDeliveriesItem(string $date, Product $product, Request $request): Response
    {
        $date = Carbon::parse($date);
        $item = Item::firstOrCreate(['name' => $request->item], ['item_origin_id' => ItemOrigin::first()->id]);
        DeliveryProductItem::firstOrCreate([
            'delivery_id' => null,
            'product_id' => $product->id,
            'item_id' => $item->id,
            'date' => $date->format('Y-m-d')
        ]);


        return \response([
            'msg' => 'ok',
            'items' => self::itemResourceForDate($date)
        ]);
    }

    /**
     * @param Delivery $delivery
     * @param Product $product
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function addItem(Delivery $delivery, Product $product, Request $request): Response|Application|ResponseFactory
    {
        $item = Item::firstOrCreate(['name' => $request->item], ['item_origin_id' => ItemOrigin::first()->id]);
        DeliveryProductItem::firstOrCreate([
            'delivery_id' => $delivery->id,
            'product_id' => $product->id,
            'item_id' => $item->id
        ]);

        $delivery->refresh();

        return \response([
            'msg' => 'ok',
            'delivery' => DeliveryResource::make($delivery),
        ]);
    }


    public function removeDeliveriesItem(string $date, Product $product, Item $item): Response
    {
        $date = Carbon::parse($date);

        DB::table('delivery_product_items')->whereDate('date', $date->format('Y-m-d'))
            ->where('product_id', $product->id)->where('item_id', $item->id)->delete();

        return \response([
            'msg' => 'ok',
            'items' => self::itemResourceForDate($date)
        ]);
    }

    /**
     * @param Delivery $delivery
     * @param Product $product
     * @param Item $item
     * @return Response|Application|ResponseFactory
     */
    public function removeItem(Delivery $delivery, Product $product, Item $item): Response|Application|ResponseFactory
    {
        DB::table('delivery_product_items')->where('delivery_id', $delivery->id)
            ->where('product_id', $product->id)->where('item_id', $item->id)->delete();
        $delivery->refresh();

        return \response([
            'msg' => 'ok',
            'delivery' => DeliveryResource::make($delivery),
        ]);
    }
}
