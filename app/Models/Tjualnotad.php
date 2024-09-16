<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tjualnotad extends Model
{
    use HasFactory;

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
