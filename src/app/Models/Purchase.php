<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'product_id',
        'payment_method',
        'ship_postal_code',
        'ship_address',
        'ship_building',
        'purchased_at',
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