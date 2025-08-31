<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'product_name',
        'price',
        'brand',
        'condition',
        'description',
        'product_image',
    ];

    public function user(){
        return $this -> belongsTo(User::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function productCategories(){
        return $this->hasMany(ProductCategory::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function likes(){
        return $this->belongsToMany(User::class, 'likes');
    }

    public function purchase(){
        return $this->hasOne(Purchase::class);
    }

    public function trades() {
        return $this->hasMany(\App\Models\Trade::class);
    }

    public function getIsSoldAttribute(): bool
    {
        return $this->trades()
            ->where('status', 'completed')
            ->exists();
    }

    public function scopeAvailable($q)
    {
        return $q->whereDoesntHave('trades', fn ($qq) => $qq->where('status', 'completed'));
    }
}