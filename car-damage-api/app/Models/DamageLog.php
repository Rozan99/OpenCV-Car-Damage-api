<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageLog extends Model
{
    use HasFactory;

    protected $fillable = ['image_path', 'score', 'level'];
}
