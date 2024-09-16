<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tworkorderh extends Model
{
    use HasFactory;

    protected $table = 'tworkorderh';
    protected $guarded = [
        'montir',
        'customer',
    ];

    public function montir()
    {
        return $this->hasOne(Mmontir::class, 'id', 'montir_id');
    }

    public function customer()
    {
        return $this->hasOne(Mcustomer::class, 'id', 'customer_id');
    }

    public function jualnota()
    {
        return $this->hasOne(Tjualnotah::class, 'workorderh_id', 'id');
    }

    public static function generateKode($prefix)
    {
        $kode = $prefix;
        $result = static::query()->where('kode', 'LIKE', $prefix . '%')->get();

        $numerator = count($result) + 1;
        $kode .= str_pad($numerator, 4, '0', STR_PAD_LEFT);

        return $kode;
    }
}
