<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tjualnotah extends Model
{
    use HasFactory;

    protected $table = 'tjualnotah';
    protected $guarded = [
        'workorder',
        'customer',
        'jualnota_detail',
    ];

    public function workorder()
    {
        return $this->hasOne(Tworkorderh::class, 'id', 'workorderh_id');
    }

    public function customer()
    {
        return $this->hasOne(Mcustomer::class, 'id', 'customer_id');
    }

    public function jualnotaDetail()
    {
        return $this->hasMany(Tjualnotad::class, 'jualnotah_id', 'id');
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
