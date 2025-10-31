<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PullJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 'filters', 'required', 'total', 'processed', 'failed', 'error'
    ];

    protected $casts = [
        'filters' => 'array',
        'required' => 'array',
        'total' => 'integer',
        'processed' => 'integer',
        'failed' => 'integer',
    ];
}

