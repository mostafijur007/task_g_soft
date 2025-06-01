<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_product');
    }
    
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }
}
