<?php
/* NOTE

status -> 0 = Order Delivered,
        1 = Order Processing,
        2 = Order InActive.

is_deleted -> 0 = Order Active,
        1 = Order Completed,
        2 = Order Cancelled.

*/

namespace App\modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\DeleteOrderRequest;
use App\Http\Requests\Order\GetOrderRequest;
use App\Http\Requests\Order\UpdateOrderProductRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\modules\Order\services\OrderService;
use App\modules\Order\BO\OrderBO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /*  Vendor can Create Order with Products. */
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        try {
            $orderBO = app(OrderBO::class);
            $orderService = app(OrderService::class);

            $orderBO->setUserId(Auth::id());
            $orderBO->setOrderNo($request->input('order_id'));
            $orderBO->setCustomerName($request->input('customer_name'));
            $orderBO->setEmail($request->input('email'));
            $orderBO->setContactNo($request->input('contact_no'));
            $orderBO->setAddress1($request->input('address1'));
            $orderBO->setAddress2($request->input('address2'));
            $orderBO->setPinCode($request->input('pin_code'));
            $orderBO->setCity($request->input('city'));
            $orderBO->setState($request->input('state'));
            $orderBO->setCountry($request->input('country'));
            $orderBO->setWeight($request->input('weight'));
            $orderBO->setLength($request->input('length'));
            $orderBO->setWidth($request->input('width'));
            $orderBO->setHeight($request->input('height'));
            $orderBO->setProducts($request->input('products'));
            // dd($orderBO);

            $response = $orderService->createOrder($orderBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message']
            ], $response['status_code']);
        } catch (\Exception $e) {
            Log::error('Order Creation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }

    /*  Vendor can GET All Order and Products with Filters. */
    public function getOrders(GetOrderRequest $request): JsonResponse
    {
        try {
            $orderBO = app(OrderBO::class);
            $orderService = app(OrderService::class);

            $orderBO->setOrderNo($request->input('order_no'));
            $orderBO->setCustomerName($request->input('customer_name'));
            $orderBO->setCreatedDate($request->input('created_date'));

            $response = $orderService->getOrders($orderBO);

            return response()->json([
                'status' => $response['status'],
                'data' => $response['data'] ?? [],
                'message' => $response['message'] ?? null
            ], $response['status_code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /*  Vendor can GET Active Order and Products - Filters. */
    public function getActiveOrders(GetOrderRequest $request): JsonResponse
    {
        $orderBO = app(OrderBO::class);
        $orderService = app(OrderService::class);

        $orderBO->setOrderNo($request->input('order_no'));
        $orderBO->setCustomerName($request->input('customer_name'));
        $orderBO->setCreatedDate($request->input('created_date'));

        $response = $orderService->getActiveOrders($orderBO);

        return response()->json([
            'status' => $response['status'],
            'data' => $response['data']
        ], $response['status_code']);
    }

    /*  Vendor can Export Filters data with Orders and Products Table */
    public function exportOrders(GetOrderRequest $request): JsonResponse
    {
        try {
            $orderBO = app(OrderBO::class);
            $orderService = app(OrderService::class);

            $orderBO->setOrderNo($request->input('order_no'));
            $orderBO->setCustomerName($request->input('customer_name'));
            $orderBO->setCreatedDate($request->input('created_date'));

            $response = $orderService->exportOrders($orderBO);

            return response()->json([
                'status'     => $response['status'],
                'file_path'  => $response['file_path'] ?? null,
                'message'    => $response['message'] ?? null
            ], $response['status_code']);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    /*  By Default = 0, updated_orders = 1 -----> Vendor can Update Order Basic Details. */
    public function updateOrder(UpdateOrderRequest $request): JsonResponse
    {
        try {
            $orderBO = app(OrderBO::class);
            $orderService = app(OrderService::class);

            // $orderBO->setUserId(Auth::id());
            $orderBO->setOrderNo($request->input('order_no'));
            $orderBO->setCustomerName($request->input('customer_name'));
            $orderBO->setEmail($request->input('email'));
            $orderBO->setContactNo($request->input('contact_no'));
            $orderBO->setAddress1($request->input('address1'));
            $orderBO->setAddress2($request->input('address2'));
            $orderBO->setPinCode($request->input('pin_code'));
            $orderBO->setCity($request->input('city'));
            $orderBO->setState($request->input('state'));
            $orderBO->setCountry($request->input('country'));

            $response = $orderService->updateOrder($orderBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message']
            ], $response['status_code']);
        } catch (\Exception $e) {
            Log::error('Order Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }

    /*  By Default = 0, updated_orders = 1 -----> Vendor can Update Products Details and Amount Calculation . */
    public function updateOrderProductDetails(UpdateOrderProductRequest $request): JsonResponse
    {
        try {
            $orderBO = app(OrderBO::class);
            $orderService = app(OrderService::class);

            $orderBO->setUserId(Auth::id());
            $orderBO->setOrderNo($request->input('order_no'));
            $orderBO->setWeight($request->input('weight'));
            $orderBO->setLength($request->input('length'));
            $orderBO->setWidth($request->input('width'));
            $orderBO->setHeight($request->input('height'));
            $orderBO->setProducts($request->input('products'));
            // dd($orderBO);

            $response = $orderService->updateOrderProductDetails($orderBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message']
            ], $response['status_code']);
        } catch (\Exception $e) {
            Log::error('Update Order Product Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }

    /*  Vendor can Soft DELETE - Order Cancelled.       status = 2, is_deleted = 2  */
    public function updateCancelOrder(DeleteOrderRequest $request): JsonResponse
    {
        try {
            $orderBO = app(OrderBO::class);
            $orderService = app(OrderService::class);

            $orderBO->setOrderNo($request->input('order_no'));

            $response = $orderService->updateCancelOrder($orderBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message']
            ], $response['status_code']);
        } catch (\Exception $e) {
            Log::error('Cancel Order Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }

    /*  Vendor can Hard DELETE - Order Cancelled or Deleted.        status = 2, is_deleted = 2  */
    public function deleteOrder(DeleteOrderRequest $request): JsonResponse
    {
        try {
            $orderBO = app(OrderBO::class);
            $orderService = app(OrderService::class);

            $orderBO->setOrderNo($request->input('order_no'));

            $response = $orderService->deleteOrder($orderBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message']
            ], $response['status_code']);
        } catch (\Exception $e) {
            Log::error('Hard Delete Order Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }


    /*  Admin and sub-Admin can Update order Status ->        status = 0, is_deleted = 1  */
    public function updateOrderStatus(Request $request): JsonResponse
    {
        try {
            $OrderBO = app(OrderBO::class);
            $OrderUpdateService = app(OrderService::class);

            $OrderBO->setOrderNo($request->input('order_no'));

            $response = $OrderUpdateService->updateOrderStatus($OrderBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message'],
            ], $response['status_code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }
}
