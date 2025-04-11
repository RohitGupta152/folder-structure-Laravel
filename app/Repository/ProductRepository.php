<?php


namespace App\Repository;


use App\Models\Product;
use App\Repository\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{

    public function storeProducts(array $products)
    {
        Product::create($products);
    }

    public function deleteProductsByOrderId(int $orderId): void
    {
        Product::where('order_table_id', $orderId)->delete();
    }
}
