<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Model
{
    protected $fillable =[
        'user_id',
        'profile_image',
        'postal_code',
        'address',
        'building',
    ];
    
    public function user(){
        return $this -> belongsTo(User::class);
    }
}
