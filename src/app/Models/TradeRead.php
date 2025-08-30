<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeRead extends Model
{
    protected $fillable = ['trade_id','user_id','last_read_message_id'];
}

