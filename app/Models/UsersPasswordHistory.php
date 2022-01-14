<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersPasswordHistory extends Model
{
    use HasFactory;

    protected $table = 'users_password_history';

    protected $fillable = [
        'user_id',
        'password',
    ];

    public function user(){
        $this->belongsTo('App\Models\User');
    }
}
