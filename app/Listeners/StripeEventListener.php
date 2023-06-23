<?php

namespace App\Listeners;

use App\Models\Plan;
use App\Models\User;
use Laravel\Cashier\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class StripeEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'charge.succeeded') {
            // $stripe_id = Arr::get($event->payload, 'data.object.customer', null);
            // $price = Arr::get($event->payload, 'data.object.amount', null);
            // Log::info("[charge.succeeded] - " . $stripe_id);
            // if(isset($stripe_id)) {
            //     $price = $price / 100;
            //     $plan = Plan::where('price', $price)->orWhere('price', $price * 0.8)->first();
            //     $user = User::where('stripe_id', $stripe_id)->first();
            //     $user->plan_id = $plan->id;
            //     $user->save();
            // }
        }
    }
}
