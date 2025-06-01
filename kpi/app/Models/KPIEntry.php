<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPIEntry extends Model
{
    /** @use HasFactory<\Database\Factories\KPIEntryFactory> */
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
