<?php

namespace App\Modules\Order\Controllers;
use App\Modules\Order\Services\OrderService;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    // public function index()
    // {
    //     return response()->json([
    //         'message' => 'Order Controller Working!'
    //     ]);
    // }


public function index()
{
    $service = new OrderService();
    return response()->json(['data' => $service->getOrderData()]);
}

}
