<?php

// app/Models/Trade.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Trade extends Model
{
    protected $fillable = ['product_id','buyer_id','seller_id','status'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function buyer(): BelongsTo { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller(): BelongsTo { return $this->belongsTo(User::class, 'seller_id'); }
    public function messages(): HasMany { return $this->hasMany(TradeMessage::class); }

    // 新着順にしたいとき用
    public function scopeLatestUpdate($q){ return $q->orderByDesc('updated_at'); }
}
