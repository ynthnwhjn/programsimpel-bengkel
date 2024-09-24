<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tstokopnameh extends Model
{
    use HasFactory;

    protected $table = 'tstokopnameh';
    protected $guarded = [
        'gudang',
        'stok_opname_detail',
    ];

    public function gudang()
    {
        return $this->hasOne(Mgudang::class, 'id', 'gudang_id');
    }

    public function stokOpnameDetail()
    {
        return $this->hasMany(Tstokopnamed::class, 'stokopnameh_id', 'id');
    }

    public static function generateKode($prefix, $jenis)
    {
        $kode = $prefix;
        $result = static::query()->where('kode', 'LIKE', $prefix . '%')->get();

        $numerator = count($result) + 1;
        $kode .= str_pad($numerator, 4, '0', STR_PAD_LEFT);

        return $kode;
    }
}
