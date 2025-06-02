<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'uom',
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_product');
    }
    
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }
}
