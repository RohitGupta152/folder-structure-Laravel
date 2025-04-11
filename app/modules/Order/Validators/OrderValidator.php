<?php

namespace App\modules\Order\Validators;

use App\Repository\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Order;

class OrderValidator
{
    public function validatorWalletBalance(float $walletBalance): void
    {
        if ($walletBalance <= 0) {
            throw new Exception(
                $walletBalance == 0
                    ? 'Recharge your Wallet Balance.'
                    : 'Negative Wallet Balance. Kindly Recharge your Wallet.'
            );
        }
    }

    public function validatorOrderExists(bool $orderExists): void
    {
        if ($orderExists) {
            throw new Exception('You cannot use this Order ID again for the same user.');
        }
    }

    public function validatorApplicableRateAmountNotFound(?float $applicableRate): void
    {
        // dd($applicableRate);
        if (is_null($applicableRate)) {
            throw new Exception('No applicable rate found for this weight.');
        }
    }

    public function validatorChargeAmount(float $walletBalance, float $chargeAmount): void
    {
        if ($walletBalance < $chargeAmount) {
            throw new Exception('Insufficient Wallet Balance. Kindly Recharge your Wallet.');
        }
    }

    public function validatorExportData($orders): void
    {
        if (empty($orders) || $orders->isEmpty()) {
            throw new Exception('No data available to export.');
        }

        foreach ($orders as $order) {
            if (empty($order->products) || $order->products->isEmpty()) {
                throw new Exception("Order {$order->order_no} has no associated products to export.");
            }
        }
    }

    public function validatorOrderNotFound(?Order $order): void
    {
        if (is_null($order)) {
            throw new Exception('Order not found.');
        }
    }

    public function validatorOrderWeight(float $oldWeight, float $chargingWeight): void
    {
        if ($oldWeight < $chargingWeight) {
            throw new Exception('Updated weight cannot exceed the original recorded weight.');
        }
    }

    public function validateOrderAlreadyCancelled($order): void
    {
        if ($order['status'] == 2 && $order['is_deleted'] == 2) {
            throw new Exception('This order is already cancelled.');
        }
    }

    public function validateOrderAlreadyDelivered($order): void
    {
        if ($order['status'] == 0 && $order['is_deleted'] == 1) {
            throw new Exception('This order has already been delivered and cannot be cancelled.');
        }
    }


    public function validatorOrderCancelFailed(bool $updated): void
    {
        if (!$updated) {
            throw new Exception('Failed to cancel the order. Please try again.');
        }
    }

    public function validatorOrderDeleteFailed(bool $deleted): void
    {
        if (!$deleted) {
            throw new Exception('Failed to delete the order. Please try again.');
        }
    }
}
