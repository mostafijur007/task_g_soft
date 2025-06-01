<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;

class CustomerService
{
    protected $repo;

    public function __construct(CustomerRepositoryInterface $repo)
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

    public function restore($id)
    {
        return $this->repo->restore($id);
    }

    public function generateCode(): string
    {
        $count = Customer::withTrashed()->count() + 1;
        return 'CUS-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
