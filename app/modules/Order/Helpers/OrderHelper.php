<?php

namespace App\modules\Order\Helpers;



class OrderHelper
{
    public static function calculateTotalAmountAndQuantity(array $products): array
    {
        $totalAmount = 0;
        $totalQuantity = 0;

        foreach ($products as $product) {
            $totalAmount += $product['price'] * $product['quantity'];
            $totalQuantity += $product['quantity'];
        }

        return [$totalAmount, $totalQuantity];
    }

    public static function calculateChargingWeight($weight, $length, $width, $height)
    {
        $volumetric = ($length * $width * $height) / 5000;
        $weight = max($weight, $volumetric);
        $decimal = $weight - floor($weight);

        if ($decimal > 0 && $decimal <= 0.5) return floor($weight) + 0.5;
        if ($decimal > 0.5) return ceil($weight);

        return $weight;
    }

    public static function formatOrders($orders): array
    {
        $formattedOrders = [];

        foreach ($orders as $order) {
            $formattedProducts = [];

            foreach ($order->products as $product) {
                $formattedProducts[] = [
                    'product_name' => $product->product_name,
                    'price' => $product->price . " Rs",
                    'quantity' => $product->quantity . " Qty",
                ];
            }

            $formattedOrders[] = [
                'order_no' => $order->order_no,
                'customer_name' => $order->customer_name,
                'email' => $order->email,
                'charged_amount' => $order->charged_amount . " Rs",
                'weight' => $order->weight . " Kg",
                'length' => $order->length . " Cm",
                'width' => $order->width . " Cm",
                'height' => $order->height . " Cm",
                'contact_no' => $order->contact_no,
                'address1' => $order->address1,
                'address2' => $order->address2,
                'pin_code' => $order->pin_code,
                'city' => $order->city,
                'state' => $order->state,
                'country' => $order->country,
                'total_amount' => $order->total_amount . " Rs",
                'total_qty' => $order->total_qty . " Qty",
                'order_date' => date('d M y  h:i A', strtotime($order->created_date)),
                'status' => $order->status == 0 ? 'Order Delivered'
                    : ($order->status == 1 ? 'Order Processing' : 'Order InActive'),
                'cancelled' => $order->is_deleted == 0 ? 'Order Active'
                    : ($order->is_deleted == 1 ? 'Order Completed' : 'Order Cancelled'),
                'products' => $formattedProducts
            ];
        }

        return $formattedOrders;
    }

    public static function formatExportOrders($orders): array
    {
        $formattedOrders = [];

        foreach ($orders as $order) {
            $isFirstRow = true;

            foreach ($order->products as $product) {
                $formattedOrders[] = [
                    'Order No'        => $isFirstRow ? $order->order_no : '',
                    'Customer Name'   => $isFirstRow ? $order->customer_name : '',
                    'Email'           => $isFirstRow ? $order->email : '',
                    'Contact No'      => $isFirstRow ? $order->contact_no : '',
                    'Address Line 1'  => $isFirstRow ? $order->address1 : '',
                    'Address Line 2'  => $isFirstRow ? $order->address2 : '',
                    'Pin Code'        => $isFirstRow ? $order->pin_code : '',
                    'City'            => $isFirstRow ? $order->city : '',
                    'State'           => $isFirstRow ? $order->state : '',
                    'Country'         => $isFirstRow ? $order->country : '',
                    'Charged Amount'  => $isFirstRow ? $order->charged_amount . " Rs" : '',
                    'Total Amount'    => $isFirstRow ? $order->total_amount . " Rs" : '',
                    'Total Quantity'  => $isFirstRow ? $order->total_qty . " Qty" : '',
                    'Weight (KG)'     => $isFirstRow ? $order->weight . " Kg" : '',
                    'Length (CM)'     => $isFirstRow ? $order->length . " Cm" : '',
                    'Width (CM)'      => $isFirstRow ? $order->width . " Cm" : '',
                    'Height (CM)'     => $isFirstRow ? $order->height . " Cm" : '',

                    'Order Status'    => $isFirstRow
                        ? ($order->status == 0
                            ? 'Order Delivered'
                            : ($order->status == 1
                                ? 'Order Processing'
                                : 'Order Inactive'))
                        : '',

                    'Current Status'  => $isFirstRow
                        ? ($order->is_deleted == 0
                            ? 'Order Active'
                            : ($order->is_deleted == 1
                                ? 'Order Completed'
                                : 'Order Cancelled'))
                        : '',

                    'Order Date'      => $isFirstRow
                        ? date('d M y h:i A', strtotime($order->created_date))
                        : '',

                    'Product Name'    => $product->product_name,
                    'Price (Rs)'      => $product->price . " Rs",
                    'Quantity'        => $product->quantity . " Qty"
                ];

                $isFirstRow = false;
            }
        }

        return $formattedOrders;
    }
}
