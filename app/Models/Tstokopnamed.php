<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tstokopnamed extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tstokopnamed';
    protected $guarded = [
        'id',
        'barang',
    ];

    public function barang()
    {
        return $this->hasOne(Mbarang::class, 'id', 'barang_id');
    }
}
