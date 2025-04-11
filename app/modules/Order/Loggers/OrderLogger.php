<?php


namespace App\modules\Order\Loggers;

use App\Models\Order;
use App\Models\Product;
use App\modules\Order\BO\OrderBO;
use App\Repository\Interfaces\OrderRepositoryInterface;
use App\Repository\Interfaces\ProductRepositoryInterface;
use App\Repository\Interfaces\RateChartRepositoryInterface;
use App\Repository\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class OrderLogger
{
    protected $orderRepositoryInterface;
    protected $productRepositoryInterface;
    protected $rateChartRepositoryInterface;
    protected $userRepositoryInterface;

    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface,
        ProductRepositoryInterface $productRepositoryInterface,
        RateChartRepositoryInterface $rateChartRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->rateChartRepositoryInterface = $rateChartRepositoryInterface;
        $this->userRepositoryInterface = $userRepositoryInterface;
    }


    public function updateWalletBalance(int $userId, float $newBalance): bool
    {
        $result = $this->userRepositoryInterface->updateWalletBalance($userId, $newBalance);
        Log::info("Updated wallet balance for user {$userId} to {$newBalance}");
        return $result;
    }

    public function createOrderWithProducts(OrderBO $orderBO)
    {
        $orderData = $orderBO->toArray();

        $order = $this->orderRepositoryInterface->createOrder($orderData);
        Log::info("Created order with ID {$order->id}");

        foreach ($orderBO->getProducts() as $product) {
            $productData = [
                'order_table_id' => $order->id,
                'order_no' => $orderBO->getOrderNo(),
                'product_name' => $product['product_name'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'created_date' => Carbon::now(),
                'updated_date' => Carbon::now(),
            ];
            $this->productRepositoryInterface->storeProducts($productData);
            Log::info("Stored product '{$product['product_name']}' for order ID {$order->id}");
        }

        return $order;
    }

    public function updateOrder($order, OrderBO $orderBO)
    {
        $result = $this->orderRepositoryInterface->updateOrder($order, $orderBO->toArray());
        return $result;
    }

    public function updateOrderWithProductsAndAmount(Order $order, OrderBO $orderBO)
    {
        $orderData = $orderBO->toArray();
        $orderCreatedDate = $order['created_date'];

        $this->orderRepositoryInterface->updateOrderWithProductsAndAmount($order, $orderData);

        $this->productRepositoryInterface->deleteProductsByOrderId($order->id);

        foreach ($orderBO->getProducts() as $product) {
            $productData = [
                'order_table_id' => $order->id,
                'order_no'       => $order->order_no,
                'product_name'   => $product['product_name'],
                'price'          => $product['price'],
                'quantity'       => $product['quantity'],
                'created_date'   => $orderCreatedDate,
                'updated_date'   => now(),
            ];
            $this->productRepositoryInterface->storeProducts($productData);
        }

        return $order;
    }
}
