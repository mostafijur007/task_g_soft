<?php

namespace App\Repositories\Interfaces;

interface KPIEntryRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function byMonth($month);
}
