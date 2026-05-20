<?php

namespace App\Services\Payment;

class NullPaymentGateway implements PaymentGatewayInterface
{
    public function initiatePayment(array $orderData): array
    {
        return [
            'success' => true,
            'message' => 'Order placed. Payment will be collected via WhatsApp.',
            'payment_url' => null,
            'gateway' => $this->getGatewayName(),
        ];
    }

    public function verifyPayment(string $paymentId): array
    {
        return [
            'success' => false,
            'message' => 'Payment verification not available for WhatsApp payment method.',
            'status' => 'unpaid',
        ];
    }

    public function handleWebhook(array $payload): array
    {
        return [
            'success' => false,
            'message' => 'Webhooks not supported for WhatsApp payment method.',
        ];
    }

    public function getGatewayName(): string
    {
        return 'whatsapp';
    }
}
