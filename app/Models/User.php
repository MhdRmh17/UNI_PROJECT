<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;  // تأكد من تثبيت sanctum

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'username',
        'email',
        'password',
        'type'
       
       
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
