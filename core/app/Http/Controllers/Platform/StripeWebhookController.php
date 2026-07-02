<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Services\Billing\StripeBillingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request, StripeBillingService $billing): Response
    {
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            return response('Webhook secret not configured.', 503);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $secret
            );
        } catch (SignatureVerificationException) {
            return response('Invalid signature.', 400);
        }

        $payload = $event->data->object;

        match ($event->type) {
            'customer.subscription.created',
            'customer.subscription.updated',
            'customer.subscription.deleted' => $billing->syncSubscriptionFromStripe($payload),
            default => null,
        };

        return response('OK', 200);
    }
}
