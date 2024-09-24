<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tlaporanstok extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tlaporanstok';
    protected $guarded = [];
}
