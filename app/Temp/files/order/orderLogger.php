// namespace App\modules\Order\Loggers;

// use App\Models\Log;
// use Illuminate\Support\Facades\Auth;

// class OrderLogger
// {
//     public function logOrderCreation($order)
//     {
//         dd($order);
//         Log::create([
//             'module' => 'Order',
//             'action' => 'Create',
//             'data' => json_encode($order),
//             'performed_by' => Auth::id(),
//             'performed_at' => now()
//         ]);
//     }
// }


















// public function getWalletBalance(int $userId): float
    // {
    //     $balance = $this->userRepository->getWalletBalance($userId);
    //     Log::info("Retrieved wallet balance for user {$userId}: {$balance}");
    //     return $balance;
    // }

    // public function checkExistingOrder(string $orderNo, int $userId)
    // {
    //     $existingOrder = $this->orderRepository->checkExistingOrder($orderNo, $userId);
    //     Log::info("Checked existing order for order number {$orderNo} and user {$userId}");
    //     return $existingOrder;
    // }


    // public function createOrder(array $orderData)
    // {
    //     $order = $this->orderRepository->createOrder($orderData);
    //     Log::info("Created order with ID {$order->id}");
    //     return $order;
    // }

    // public function storeProduct(int $orderId, string $orderNo, array $productData)
    // {
    //     // Add order-related identifiers to the product data
    //     $productData['order_table_id'] = $orderId;
    //     $productData['order_no'] = $orderNo;
    //     $currentTimestamp = now();
    //     $productData['created_date'] = $currentTimestamp;
    //     $productData['updated_date'] = $currentTimestamp;
    
    //     $this->productRepository->storeProducts($productData);

    //     Log::info("Stored product for order ID {$orderId} with order number {$orderNo}");
    // }    

    // public function getApplicableRate(float $chargingWeight, int $userId): ?float
    // {
    //     $rates = $this->rateChartRepository->getRateForWeight($chargingWeight, $userId);
    //     Log::info("Retrieved applicable rates for charging weight {$chargingWeight} and user {$userId}");

    //     if ($rates->isEmpty()) {
    //         return null;
    //     }

    //     $userRates = $rates->where('user_id', $userId);
    //     $defaultRates = $rates->where('user_id', 0);

    //     $rate = $userRates->where('weight', '==', $chargingWeight)->first()
    //         ?? $defaultRates->where('weight', '==', $chargingWeight)->first()
    //         ?? $defaultRates->sortByDesc('weight')->first();

    //     $rateAmount = $rate ? $rate->rate_amount : null;
    //     Log::info("Determined applicable rate: {$rateAmount}");
    //     return $rateAmount;
    // }