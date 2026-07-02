<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Stripe\StripeClient;

class StripeBillingService
{
    protected ?StripeClient $stripe = null;

    public function client(): StripeClient
    {
        if ($this->stripe === null) {
            $this->stripe = new StripeClient(config('services.stripe.secret'));
        }

        return $this->stripe;
    }

    public function createCheckoutSession(Tenant $tenant, Plan $plan, string $successUrl, string $cancelUrl): ?string
    {
        if (!$plan->stripe_price_id || !config('services.stripe.secret')) {
            return null;
        }

        $customerId = $this->ensureCustomer($tenant);

        $session = $this->client()->checkout->sessions->create([
            'customer' => $customerId,
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'tenant_id' => $tenant->id,
            ],
        ]);

        return $session->url;
    }

    public function ensureCustomer(Tenant $tenant): string
    {
        if ($tenant->stripe_id) {
            return $tenant->stripe_id;
        }

        $customer = $this->client()->customers->create([
            'name' => $tenant->name,
            'metadata' => ['tenant_id' => $tenant->id, 'slug' => $tenant->slug],
        ]);

        $tenant->update(['stripe_id' => $customer->id]);

        return $customer->id;
    }

    public function syncSubscriptionFromStripe(object $stripeSubscription): void
    {
        $tenantId = $stripeSubscription->metadata->tenant_id ?? null;

        if (!$tenantId) {
            $tenant = Tenant::query()->where('stripe_id', $stripeSubscription->customer)->first();
            $tenantId = $tenant?->id;
        }

        if (!$tenantId) {
            return;
        }

        Subscription::updateOrCreate(
            ['stripe_id' => $stripeSubscription->id],
            [
                'tenant_id' => $tenantId,
                'type' => $stripeSubscription->items->data[0]->price->recurring->interval ?? 'default',
                'stripe_status' => $stripeSubscription->status,
                'stripe_price' => $stripeSubscription->items->data[0]->price->id ?? null,
                'quantity' => $stripeSubscription->items->data[0]->quantity ?? 1,
                'trial_ends_at' => isset($stripeSubscription->trial_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                    : null,
                'ends_at' => isset($stripeSubscription->cancel_at)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at)
                    : null,
            ]
        );

        $status = match ($stripeSubscription->status) {
            'active' => Tenant::STATUS_ACTIVE,
            'trialing' => Tenant::STATUS_TRIALING,
            'past_due', 'unpaid' => 'past_due',
            'canceled' => Tenant::STATUS_SUSPENDED,
            default => null,
        };

        if ($status) {
            Tenant::whereKey($tenantId)->update(['status' => $status]);
        }
    }
}
