<?php

namespace App\Providers;

use App\Models\Buy;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Order;
use App\Observers\BuyObserver;
use App\Observers\CustomerObserver;
use App\Observers\DeliveryObserver;
use App\Observers\DeliveryServiceObserver;
use App\Observers\OrderObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Delivery::observe(DeliveryObserver::class);
        Customer::observe(CustomerObserver::class);
        Order::observe(OrderObserver::class);
        Buy::observe(BuyObserver::class);
        DeliveryService::observe(DeliveryServiceObserver::class);
    }
}
