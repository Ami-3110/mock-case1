<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TradeRating extends Model
{
    use HasFactory;

    protected $fillable = ['trade_id','rater_id','ratee_id','score'];
}
