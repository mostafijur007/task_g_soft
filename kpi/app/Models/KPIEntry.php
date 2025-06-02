<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KPIEntry extends Model
{
    /** @use HasFactory<\Database\Factories\KPIEntryFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'month',
        'customer_id',
        'product_id',
        'supplier_id',
        'uom',
        'quantity',
        'asp',
        'total_value',
    ];

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
