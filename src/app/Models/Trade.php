<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Trade extends Model
{
    use HasFactory;
    
    protected $fillable = ['product_id','buyer_id','seller_id','status'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function buyer(): BelongsTo { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller(): BelongsTo { return $this->belongsTo(User::class, 'seller_id'); }
    public function messages(): HasMany { return $this->hasMany(TradeMessage::class); }

    public function scopeLatestUpdate($q){ return $q->orderByDesc('updated_at'); }
}
