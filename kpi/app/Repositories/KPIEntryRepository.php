<?php

namespace App\Repositories;

use App\Models\KPIEntry;
use App\Repositories\Interfaces\KPIEntryRepositoryInterface;

class KPIEntryRepository implements KPIEntryRepositoryInterface
{
    public function all($perPage = 15)
    {
        return KPIEntry::with('customer', 'product', 'supplier')->paginate($perPage);
    }

    public function find($id)
    {
        return KPIEntry::findOrFail($id);
    }

    public function create(array $data)
    {
        return KPIEntry::create($data);
    }

    public function update($id, array $data)
    {
        $kpi = KPIEntry::findOrFail($id);
        $kpi->update($data);
        return $kpi;
    }

    public function delete($id)
    {
        return KPIEntry::findOrFail($id)->delete();
    }

    public function byMonth($perPage=10)
    {
        return KPIEntry::with('customer', 'product', 'supplier')->where('month', $month)->paginate($perPage);
    }
}
