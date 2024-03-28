<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'active',
        'name',
        'description',
        'deadline',
        'status',
        'priority', // 0: Baixa, 1: Média, 2: Alta
        'tag',
    ];

    protected $dates = [
        'deleted_at'
    ];
}


