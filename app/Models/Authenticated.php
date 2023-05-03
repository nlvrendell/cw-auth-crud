<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Authenticated extends Authenticatable
{
    use HasFactory;

    protected $table = 'authenticated';

    protected $fillable = [
        'username',
        'user',
        'territory',
        'domain',
        'department',
        'uid',
        'login',
        'scope',
        'user_email',
        'name',
        'access_token',
        'expires_in',
        'token_type',
        'refresh_token',
        'client_id',
        'apiversion',
        'api_password',
        'token_expired_in',
    ];
}
