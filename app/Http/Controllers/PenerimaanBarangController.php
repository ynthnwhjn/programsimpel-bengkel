<?php

namespace App\Http\Controllers;

use App\Models\Tlaporanstok;
use App\Models\Tstokopnamed;
use App\Models\Tstokopnameh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PenerimaanBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $items = Tstokopnameh::query()
                ->with([
                    'gudang',
                ]);

            return DataTables::of($items)
                ->filter(function($query) {
                    if(request()->filled('search.value')) {
                        return $query->where('nama', 'LIKE', '%'. request('search.value') .'%');
                    }

                    return $query;
                })
                ->toJson();
        }

        return view('penerimaan_barang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('penerimaan_barang.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $tanggal = Carbon::parse($request->input('tanggal'))->toDateString();
            $prefix = 'BTB/' . Carbon::parse($request->input('tanggal'))->format('y/m/');
            $kode = Tstokopnameh::generateKode($prefix, $request->input('jenis'));

            $request->merge([
                'tanggal' => $tanggal,
                'kode' => $kode,
            ]);

            $item = new Tstokopnameh($request->all());
            $item->save();

            foreach ($request->input('stok_opname_detail', []) as $row) {
                $item_detail = new Tstokopnamed($row);
                $item->stokOpnameDetail()->save($item_detail);

                $stok = new Tlaporanstok([
                    'stokopnameh_id' => $item->id,
                    'stokopnamed_id' => $item_detail->id,
                    'gudang_id' => $item->gudang_id,
                    'barang_id' => $item_detail->barang_id,
                    'jumlah' => $item_detail->jumlah,
                    'tanggal' => Carbon::now(),
                ]);
                $stok->save();
            }

            DB::commit();

            $redirect_to = route('penerimaan_barang.show', $item);

            return response()->json([
                'redirect_to' => $redirect_to,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Tstokopnameh::query()
            ->with([
                'gudang',
                'stokOpnameDetail.barang',
            ])
            ->find($id);

        return view('penerimaan_barang.form', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Tstokopnameh::query()
            ->with([
                'gudang',
                'stokOpnameDetail.barang',
            ])
            ->find($id);

        return view('penerimaan_barang.form', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $tanggal = Carbon::parse($request->input('tanggal'))->toDateString();

            $request->merge([
                'tanggal' => $tanggal,
            ]);

            $item = Tstokopnameh::query()->find($id);
            $item->update($request->all());

            $item->stokOpnameDetail()->delete();
            Tlaporanstok::query()
                ->where('stokopnameh_id', $item->id)
                ->delete();

            foreach ($request->input('stok_opname_detail', []) as $row) {
                $item_detail = new Tstokopnamed($row);
                $item->stokOpnameDetail()->save($item_detail);

                $stok = new Tlaporanstok([
                    'stokopnameh_id' => $item->id,
                    'stokopnamed_id' => $item_detail->id,
                    'gudang_id' => $item->gudang_id,
                    'barang_id' => $item_detail->barang_id,
                    'jumlah' => $item_detail->jumlah,
                    'tanggal' => Carbon::now(),
                ]);
                $stok->save();
            }

            DB::commit();

            $redirect_to = route('penerimaan_barang.show', $item);

            return response()->json([
                'redirect_to' => $redirect_to,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
