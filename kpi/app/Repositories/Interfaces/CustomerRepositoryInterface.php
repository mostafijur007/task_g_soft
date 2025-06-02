<?php

namespace App\Repositories\Interfaces;

interface CustomerRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);

    public function assignProducts($customerId, array $productIds);
    public function getAssignedProducts($customerId);
    public function removeProduct($customerId, $productId);
}
