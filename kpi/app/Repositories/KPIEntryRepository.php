<?php

namespace App\Repositories;

use App\Models\KPIEntry;
use App\Repositories\Interfaces\KPIEntryRepositoryInterface;

class KPIEntryRepository implements KPIEntryRepositoryInterface
{
    public function all()
    {
        return KPIEntry::all();
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

    public function byMonth($month)
    {
        return KPIEntry::where('month', $month)->get();
    }
}
