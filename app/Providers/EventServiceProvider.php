<?php

namespace App\Providers;

use App\Events\OnlineOrderPaid;
use App\Events\OnlineOrderSaved;
use App\Events\PurchaseBillCreated;
use App\Events\SalesInvoiceCreated;
use Illuminate\Auth\Events\Registered;
use App\Events\SalesInvoicePaymentSaved;
use App\Listeners\AssetsManagementHandler;
use App\Listeners\CreateReceiptTransferOrder;
use App\Listeners\CreateDeliveryTransferOrder;
use App\Listeners\OnlineOrders\CreateShippingOrder;
use App\Listeners\OnlineOrders\CreateInvoiceAutomatically;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Listeners\Sales\Invoices\Payments\CreatePaymentJournalEntries;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\OnlineOrders\CreateDeliveryTransferOrder as OnlineOrdersCreateDeliveryTransferOrder;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
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
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
