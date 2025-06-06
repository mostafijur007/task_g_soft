<?php

namespace Database\Seeders;

use App\Models\KPIEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KPIEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KPIEntry::factory()->count(50)->create();
    }
}
