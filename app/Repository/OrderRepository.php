<?php


namespace App\Repository;


use App\Models\User;
use App\Models\Order;
use App\Repository\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function checkExistingOrder(string $orderId, string $user_id)
    {
        return Order::where('order_no', $orderId)
            ->where('user_id', $user_id)
            ->get();
    }

    public function createOrder(array $orderData)
    {
        return Order::create($orderData);
    }

    public function getOrders(array $filters, int $userId)
    {
        $query = Order::with('products')->where('user_id', $userId);

        if (!empty($filters['order_no'])) {
            $query->where('order_no', $filters['order_no']);
        }

        if (!empty($filters['customer_name'])) {
            $query->where('customer_name', 'LIKE', "%{$filters['customer_name']}%");
        }

        if (!empty($filters['created_date'])) {
            $dates = explode(' ', $filters['created_date']);

            if (count($dates) === 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));

                $query->whereBetween('created_date', [$startDate, $endDate]);
            }
        }

        return $query->get();
    }

    public function getActiveOrders(array $filters, int $userId)
    {
        $query = Order::with('products')->where('user_id', $userId);

        if (!empty($filters['order_no'])) {
            $query->where('order_no', $filters['order_no']);
        }

        if (!empty($filters['customer_name'])) {
            $query->where('customer_name', 'LIKE', "%{$filters['customer_name']}%");
        }

        if (!empty($filters['created_date'])) {
            $dates = explode(' ', $filters['created_date']);

            if (count($dates) === 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));

                $query->whereBetween('created_date', [$startDate, $endDate]);
            }
        }

        return $query->where([
            ['status', '=', 1],
            ['is_deleted', '=', 0],
            ['user_id', '=', $userId]
        ])->get();
    }

    public function findOrder(string $orderNo, int $userId)
    {
        return Order::where('order_no', $orderNo)
            ->where('user_id', $userId)
            ->get();
    }

    public function updateOrder(Order $order, array $orderData)
    {
        $order->update([
            'customer_name' => $orderData['customer_name'],
            'email' => $orderData['email'],
            'contact_no' => $orderData['contact_no'],
            'address1' => $orderData['address1'],
            'address2' => $orderData['address2'],
            'pin_code' => $orderData['pin_code'],
            'city' => $orderData['city'],
            'state' => $orderData['state'],
            'country' => $orderData['country'],
            'updated_orders' => 1,
            'updated_date' => now()
        ]);
    }

    public function updateOrderWithProductsAndAmount(Order $order, array $orderData)
    {
        $order->update([
            'weight'          => $orderData['weight'],
            'length'          => $orderData['length'],
            'width'           => $orderData['width'],
            'height'          => $orderData['height'],

            'total_amount'     => $orderData['total_amount'],
            'total_qty'        => $orderData['total_qty'],
            'charged_amount'   => $orderData['charged_amount'],
            'charged_weight'   => $orderData['charged_weight'],

            'updated_orders' => 1,
            'updated_date' => now()
        ]);
    }

    public function updateCancellation(Order $order)
    {
        return $order->update([
            'status' => 2,
            'is_deleted' => 2,
            'updated_date' => now()
        ]);
    }

    public function deleteOrder(Order $order): bool
    {
        $order->products()->delete();
        return $order->delete();
    }



    public function updateOrderStatus(Order $order)
    {
        $order['status'] = 0;
        $order['is_deleted'] = 1;
        $order['updated_date'] = now();
        $order->save();
    }
}
