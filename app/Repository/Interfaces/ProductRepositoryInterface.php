<?php

namespace App\Repository\Interfaces;


interface ProductRepositoryInterface
{
    public function storeProducts(array $products);

    public function deleteProductsByOrderId(int $orderId): void;
}
