<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LaporanRingkasanStokController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            $items = DB::table('tlaporanstok', 'a')
                ->whereNull('a.deleted_at')
                ->join('mbarang', 'mbarang.id', '=', 'a.barang_id')
                ->selectRaw("
                    a.gudang_id,
                    a.barang_id,
                    SUM(
                        CASE
                            WHEN a.jumlah > 0
                            THEN a.jumlah
                            ELSE 0
                        END
                    ) AS masuk,
                    SUM(
                        CASE
                            WHEN a.jumlah < 0
                            THEN a.jumlah
                            ELSE 0
                        END
                    ) AS keluar,
                    SUM(a.jumlah) AS saldo,
                    mbarang.nama AS barang
                ")
                ->groupByRaw('
                    a.gudang_id,
                    a.barang_id
                ');

            return DataTables::of($items)
                ->toJson();
        }

        return view('laporan.laporan_ringkasan_stok');
    }
}
