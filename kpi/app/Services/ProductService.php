<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductService
{
    protected $repo;

    public function __construct(ProductRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->all();
    }

    public function store($data)
    {
        $data['code'] = $this->generateCode();
        return $this->repo->create($data);
    }

    public function update($id, $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

     public function generateCode(): string
    {
        $count = Product::withTrashed()->count() + 1;
        return 'PROD-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
