<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable =[
        'user_id',
        'product_id',
        'payment_method',
        'ship_postal_code',
        'ship_address',
        'ship_building',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];    

    public function user(){
        return $this -> belongsTo(User::class);
    }
    public function product(){
        return $this -> belongsTo(Product::class);

    }
}