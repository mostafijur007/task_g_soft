<?php

namespace App\Services;

use App\Repositories\Interfaces\KPIEntryRepositoryInterface;

class KPIEntryService
{
    protected $repo;

    public function __construct(KPIEntryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->all();
    }

    public function store($data)
    {
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

    public function getByMonth($month)
    {
        return $this->repo->byMonth($month);
    }
}
