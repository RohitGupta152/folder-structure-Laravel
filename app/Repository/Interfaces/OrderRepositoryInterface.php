<?php

namespace App\Repository\Interfaces;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function checkExistingOrder(string $orderId, string $user_id);

    public function createOrder(array $orderData);

    public function getOrders(array $filters, int $userId);

    public function getActiveOrders(array $filters, int $userId);

    public function findOrder(string $orderNo, int $userId);

    public function updateOrder(Order $order, array $orderData);

    public function updateOrderWithProductsAndAmount(Order $order, array $orderData);

    public function updateCancellation(Order $order);

    public function deleteOrder(Order $order): bool;



    public function updateOrderStatus(Order $order);
}
