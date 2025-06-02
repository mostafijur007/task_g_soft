<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{

    public function all($perPage = 15)
    {
        return Customer::paginate($perPage);
    }

    public function find($id)
    {
        return Customer::findOrFail($id);
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function update($id, array $data)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return true;
    }

    public function restore($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();
        return true;
    }

    public function assignProducts($customerId, array $productIds)
    {
        $customer = Customer::findOrFail($customerId);
        $customer->products()->syncWithoutDetaching($productIds);
    }

    public function getAssignedProducts($customerId)
    {
        if (!Customer::where('id', $customerId)->exists()) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Customer not found");
        }
        return Customer::findOrFail($customerId)->products;
    }

    public function removeProduct($customerId, $productId)
    {
        $customer = Customer::findOrFail($customerId);

        if (!$customer->products()->detach($productId)) {
            throw new \Exception("Detach failed");
        }
    }
}
