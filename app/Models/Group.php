<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_group',
        'title',
        'image',
        'progress',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
