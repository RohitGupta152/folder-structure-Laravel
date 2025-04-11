<?php

namespace App\Modules\Order\services;

use App\modules\Order\BO\OrderBO;
use App\modules\Order\Helpers\OrderHelper;
use App\modules\Order\Loggers\OrderLogger;
use App\modules\Order\Validators\OrderValidator;
use App\Repository\Interfaces\OrderRepositoryInterface;
use App\Repository\Interfaces\ProductRepositoryInterface;
use App\Repository\Interfaces\RateChartRepositoryInterface;
use App\Repository\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class OrderService
{
    protected $orderRepositoryInterface;
    protected $productRepositoryInterface;
    protected $rateChartRepositoryInterface;
    protected $userRepositoryInterface;
    protected $orderValidator;
    protected $orderHelper;
    protected $orderLogger;

    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface,
        ProductRepositoryInterface $productRepositoryInterface,
        RateChartRepositoryInterface $rateChartRepositoryInterface,
        UserRepositoryInterface $userRepositoryInterface,
        OrderValidator $orderValidator,
        OrderHelper $orderHelper,
        OrderLogger $orderLogger
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->rateChartRepositoryInterface = $rateChartRepositoryInterface;
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->orderValidator = $orderValidator;
        $this->orderHelper = $orderHelper;
        $this->orderLogger = $orderLogger;
    }


    public function createOrder(OrderBO $orderBO)
    {
        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();

            $currentWalletBalance = $this->userRepositoryInterface->getWalletBalance($currentUserId);
            $this->orderValidator->validatorWalletBalance($currentWalletBalance);

            $checkExistingOrder = $this->orderRepositoryInterface->checkExistingOrder($orderBO->getOrderNo(), $currentUserId);
            $orderExists = $checkExistingOrder->isNotEmpty();
            $this->orderValidator->validatorOrderExists($orderExists);

            list($totalOrderAmount, $totalProductQuantity) = $this->orderHelper->calculateTotalAmountAndQuantity($orderBO->getProducts());

            $calculatedChargingWeight = $this->orderHelper->calculateChargingWeight(
                $orderBO->getWeight(),
                $orderBO->getLength(),
                $orderBO->getWidth(),
                $orderBO->getHeight()
            );

            $applicableChargeAmount = $this->getApplicableRate($calculatedChargingWeight, $currentUserId);

            $this->orderValidator->validatorApplicableRateAmountNotFound($applicableChargeAmount);
            $this->orderValidator->validatorChargeAmount($currentWalletBalance, $applicableChargeAmount);

            $newBalance = $currentWalletBalance - $applicableChargeAmount;
            $this->orderLogger->updateWalletBalance($currentUserId, $newBalance);

            // Prepare order data
            $orderBO->prepareOrderData(
                $totalOrderAmount,
                $totalProductQuantity,
                $applicableChargeAmount,
                $calculatedChargingWeight
            );
            // dd($orderBO);

            // Create order and products
            $order = $this->orderLogger->createOrderWithProducts($orderBO);

            if ($order) {
                Log::info('Order created successfully');
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order created successfully',
                'status_code' => 200
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Failed to create order: ' . $exception->getMessage(),
                'status_code' => 500
            ];
        }
    }

    private function getApplicableRate(float $chargingWeight, int $userId): ?float
    {
        $rates = $this->rateChartRepositoryInterface->getRateForWeight($chargingWeight, $userId);

        if ($rates->isEmpty()) {
            return null;
        }

        $userRates = $rates->where('user_id', $userId);
        $defaultRates = $rates->where('user_id', 0);

        $rate = $userRates->where('weight', '==', $chargingWeight)->first();

        if (empty($rate)) {
            $rate = $defaultRates->where('weight', '==', $chargingWeight)->first();
        }

        if (empty($rate) && $defaultRates->isNotEmpty()) {
            $rate = $defaultRates->sortByDesc('weight')->first();
        }

        return $rate ? $rate->rate_amount : null;
    }

    public function getOrders(OrderBO $orderBO): array
    {
        try {
            $userId = Auth::id();
            $filters = $orderBO->toArray();

            $orders = $this->orderRepositoryInterface->getOrders($filters, $userId);
            $formattedOrders = $this->orderHelper->formatOrders($orders);

            return [
                'status' => 'success',
                'data' => $formattedOrders,
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to retrieve orders: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function exportOrders(OrderBO $orderBO): array
    {
        try {
            $userId = Auth::id();
            $filters = $orderBO->toArray();

            $orders = $this->orderRepositoryInterface->getOrders($filters, $userId);
            $this->orderValidator->validatorExportData($orders);

            $formattedData = $this->orderHelper->formatExportOrders($orders);
            $filePath = storage_path('app/public/filtered_Orders.csv');

            (new FastExcel(collect($formattedData)))->export($filePath);

            return [
                'status'      => 'success',
                'file_path'   => asset('storage/filtered_Orders.csv'),
                'message'     => 'Orders exported successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            return [
                'status'      => 'error',
                'message'     => 'Failed to export orders: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function getActiveOrders(OrderBO $orderBO): array
    {
        try {
            $userId = Auth::id();
            $filters = $orderBO->toArray();
            $orders = $this->orderRepositoryInterface->getActiveOrders($filters, $userId);

            if ($orders->isEmpty()) {
                return [
                    'status' => 'success',
                    'data' => [],
                    'status_code' => 200,
                ];
            }

            $formattedOrders = $this->orderHelper->formatOrders($orders);

            return [
                'status' => 'success',
                'data' => $formattedOrders,
                'status_code' => 200,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting active orders: ' . $e->getMessage());
            return [
                'status' => 'error',
                'data' => [],
                'status_code' => 500,
                'message' => 'Something went wrong',
            ];
        }
    }

    public function updateOrder(OrderBO $orderBO)
    {
        try {

            $userId = Auth::id();
            $order = $this->orderRepositoryInterface->findOrder($orderBO->getOrderNo(), $userId)->first();
            $this->orderValidator->validatorOrderNotFound($order);

            $this->orderLogger->updateOrder($order, $orderBO);

            return [
                'status' => 'success',
                'message' => 'Order updated successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            Log::error('Order Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred : ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function updateOrderProductDetails(OrderBO $orderBO)
    {
        try {
            $userId = $orderBO->getUserId();
            $order = $this->orderRepositoryInterface
                ->findOrder($orderBO->getOrderNo(), $userId)
                ->first();

            $this->orderValidator->validatorOrderNotFound($order);

            [$totalAmount, $totalQty] = $this->orderHelper->calculateTotalAmountAndQuantity($orderBO->getProducts());
            $chargingWeight = $this->orderHelper->calculateChargingWeight(
                $orderBO->getWeight(),
                $orderBO->getLength(),
                $orderBO->getWidth(),
                $orderBO->getHeight()
            );

            $applicableChargeAmount = $this->getApplicableRate($chargingWeight, $userId);
            $this->orderValidator->validatorApplicableRateAmountNotFound($applicableChargeAmount);

            $this->orderValidator->validatorOrderWeight($order['charged_weight'], $chargingWeight);

            $orderBO->setTotalQty($totalQty);
            $orderBO->setTotalAmount($totalAmount);
            $orderBO->setChargedAmount($applicableChargeAmount);
            $orderBO->setChargedWeight($chargingWeight);

            $this->orderLogger->updateOrderWithProductsAndAmount($order, $orderBO);

            return [
                'status' => 'success',
                'message' => 'Order calculation and product details updated successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            Log::error('Update Order Product Service Error: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function updateCancelOrder(OrderBO $orderBO): array
    {
        try {
            $userId = Auth::id();

            $order = $this->orderRepositoryInterface->findOrder($orderBO->getOrderNo(), $userId)->first();
            $this->orderValidator->validatorOrderNotFound($order);

            $this->orderValidator->validateOrderAlreadyCancelled($order);
            $this->orderValidator->validateOrderAlreadyDelivered($order);

            $canceledUpdate = $this->orderRepositoryInterface->updateCancellation($order);
            $this->orderValidator->validatorOrderCancelFailed($canceledUpdate);

            return [
                'status' => 'success',
                'message' => 'Order cancelled successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            Log::error('Order Cancel Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred : ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function deleteOrder(OrderBO $orderBO): array
    {
        try {
            $userId = Auth::id();

            $order = $this->orderRepositoryInterface->findOrder($orderBO->getOrderNo(), $userId)->first();
            $this->orderValidator->validatorOrderNotFound($order);

            $deleted = $this->orderRepositoryInterface->deleteOrder($order);
            $this->orderValidator->validatorOrderDeleteFailed($deleted);

            return [
                'status' => 'success',
                'message' => 'Order deleted successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            Log::error('Hard Delete Order Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred : ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }


    public function updateOrderStatus(OrderBO $orderBO): array
    {
        try {
            $userId = Auth::id();
            $order = $this->orderRepositoryInterface
                ->findOrder($orderBO->getOrderNo(), $userId)
                ->first();

            $this->orderValidator->validatorOrderNotFound($order);

            $this->orderRepositoryInterface->updateOrderStatus($order);

            return [
                'status' => 'success',
                'message' => 'Order delivered successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'status_code' => $e->getCode() > 0 ? $e->getCode() : 500
            ];
        }
    }
}
