<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_secret',
        'client_id',
        'redirect_uri',
        'browser_url',
        'access_token',
        'vk_token_expires',
        'count',
        'worked',
        'need_token'
    ];
}
