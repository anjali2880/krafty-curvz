<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function initiatePayment(array $orderData): array;
    public function verifyPayment(string $paymentId): array;
    public function handleWebhook(array $payload): array;
    public function getGatewayName(): string;
}
