<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tjualnotad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tjualnotad';
    protected $guarded = [
        'id',
        'barang',
    ];

    public function barang()
    {
        return $this->hasOne(Mbarang::class, 'id', 'barang_id');
    }
}
