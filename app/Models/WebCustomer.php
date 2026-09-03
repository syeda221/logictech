<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class WebCustomer extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'customer_id', 'name', 'email', 'phone', 'password', 'address', 'city', 'state', 'country', 'zip_code'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
