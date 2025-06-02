<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function all($perPage = 15)
    {
        return Product::with(['customers', 'suppliers'])->paginate($perPage);
    }

    public function find($id)
    {
        return Product::with(['customers', 'suppliers'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return Product::findOrFail($id)->delete();
    }
}
