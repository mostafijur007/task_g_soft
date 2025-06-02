<?php

namespace App\Repositories\Interfaces;

interface ProductRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    public function assignSuppliers($productId, array $supplierIds);
    public function getAssignedSuppliers($productId);
    public function removeSupplier($productId, $supplierId);
}
