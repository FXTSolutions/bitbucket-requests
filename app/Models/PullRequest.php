<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PullRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_origin',
        'title',
        'branch',
        'author',
        'created_on',
        'updated_on',
        'observation'
    ];

    protected $casts = [
        'created_on' => 'date',
        'updated_on' => 'date',
    ];
}
