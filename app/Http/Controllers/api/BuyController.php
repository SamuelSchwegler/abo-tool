<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuyResource;
use App\Http\Resources\CustomerResource;
use App\Jobs\CreateOrdersForBuy;
use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\Product;
use App\Notifications\ConfirmPayment;
use App\Notifications\SendInvoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use function response;
use Symfony\Component\Mailer\Exception\TransportException;

class BuyController extends Controller
{
    public function buy(Buy $buy)
    {
        return response(['buy' => BuyResource::make($buy)]);
    }

    public function update(Buy $buy, Request $request): Response
    {
        $validated = $request->validate([
            'paid' => ['nullable', 'boolean'],
            'discount' => ['nullable', 'numeric', 'between:0,100'],
        ]);
        $buy->update($validated);

        if ($request->has('paid') && $buy->paid) {
            CreateOrdersForBuy::dispatch($buy);
            if (!is_null($buy->customer->user)) {
                try {
                    $buy->customer->user->notify(new ConfirmPayment($buy));
                } catch (TransportException $exception) {
                    Log::error($exception->getMessage()); // zbsp falls Mailserver nicht will
                }
            }
        }

        return response(['buy' => BuyResource::make($buy)]);
    }

    public function delete(Buy $buy): Response
    {
        $buy->delete();

        return response([
            'msg' => 'ok',
        ]);
    }

    public function payments(): Response
    {
        $buys = Buy::orderBy('issued')->where(function ($query) {
            $query->where('paid', 0)->orWhere('updated_at', '>=', now()->subWeeks(2));
        })->get();

        return response([
            'buys' => BuyResource::collection($buys),
        ]);
    }

    /**
     * Manuelles Ausstellen einer neuen Rechnung. Bspw. weil Guthaben nicht mehr so hoch ist.
     *
     * @param Request $request
     * @return Response
     */
    public function issue(Request $request): Response
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'bundle_id' => ['nullable', 'exists:bundles,id']
        ]);
        $customer = Customer::find($validated['customer_id']);
        $product = Product::find($validated['product_id']);

        if (is_null($validated['bundle_id'])) {
            // Was ist das Standardbundle für den Customer mit diesem Produkt?
            // Trial Bundles sollen nicht verlängert werden.
            $bundle = $customer->buys()->whereHas('bundle', function ($query) use ($product) {
                $query->where('product_id', $product->id)->where('trial', 0);
            })->first()?->bundle ?? Bundle::where('trial', 0)->where('product_id', $product->id)->first();
        } else {
            $bundle = Bundle::find($validated['bundle_id']);
        }

        $delivery_service = $customer->delivery_service();

        $buy = Buy::create([
            'customer_id' => $customer->id,
            'bundle_id' => $bundle->id,
            'price' => $bundle->price,
            'delivery_cost' => ($delivery_service?->delivery_cost ?? 0) * $bundle->deliveries,
            'issued' => now(),
        ]);

        $user = $customer->user;
        if (!is_null($user)) {
            $user->notify(new SendInvoice($buy, true));
        }

        $customer->refresh();

        return response([
            'msg' => 'ok',
            'customer' => CustomerResource::make($customer),
        ]);
    }

    public function customer(Customer $customer): Response
    {
        $buys = $customer->buys;

        return response([
            'msg' => 'ok',
            'customer' => CustomerResource::make($customer),
            'buys' => BuyResource::collection($buys),
        ]);
    }
}
