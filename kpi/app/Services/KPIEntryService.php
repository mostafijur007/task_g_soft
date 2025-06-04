<?php

namespace App\Services;

use App\Models\KPIEntry;
use App\Repositories\Interfaces\KPIEntryRepositoryInterface;
use Illuminate\Support\Facades\Log;

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

    public function getByMonth($month)
    {
        return $this->repo->byMonth($month);
    }

    public function generateCode(): string
    {
        $count = KPIEntry::withTrashed()->count() + 1;
        Log::info($count);
        return 'KPI-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function bulkStore(array $entries)
    {
        $created = [];

        foreach ($entries as $entry) {
            $created[] = KPIEntry::create([
                ...$entry,
                'code' => $this->generateCode(),
            ]);
        }

        return $created;
    }

    public function getTrashed()
    {
        return $this->repo->getAllTrashed();
    }

    public function bulkUpdate(array $entries)
    {
        $updated = [];
        foreach ($entries as $entry) {
            $kpi = $this->repo->find($entry['id']);
            if (!$kpi) continue;
            $kpi->quantity = $entry['quantity'] ?? $kpi->quantity;
            $kpi->asp = $entry['asp'] ?? $kpi->asp;
            $kpi->uom = $entry['uom'] ?? $kpi->uom;

            if (isset($entry['quantity']) || isset($entry['asp'])) {
                $kpi->total_value = $kpi->quantity * $kpi->asp;
            }

            $kpi->save();
            $updated[] = $kpi;
        }
        return $updated;
    }

    public function restore($id)
    {
        return $this->repo->restore($id);
    }
}
