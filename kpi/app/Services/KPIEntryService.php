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
}
