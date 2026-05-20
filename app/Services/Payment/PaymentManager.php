<?php

namespace App\Services\Payment;

use Illuminate\Support\Manager;

class PaymentManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('payment.default', 'whatsapp');
    }

    protected function createWhatsappDriver(): NullPaymentGateway
    {
        return new NullPaymentGateway();
    }

    protected function createRazorpayDriver(): PaymentGatewayInterface
    {
        $className = '\\App\\Services\\Payment\\Gateways\\RazorpayGateway';
        if (class_exists($className)) {
            return new $className(config('payment.gateways.razorpay', []));
        }
        throw new \InvalidArgumentException('Razorpay gateway not installed. Please install the razorpay/razorpay package.');
    }

    protected function createStripeDriver(): PaymentGatewayInterface
    {
        $className = '\\App\\Services\\Payment\\Gateways\\StripeGateway';
        if (class_exists($className)) {
            return new $className(config('payment.gateways.stripe', []));
        }
        throw new \InvalidArgumentException('Stripe gateway not installed. Please install the stripe/stripe-php package.');
    }
}
