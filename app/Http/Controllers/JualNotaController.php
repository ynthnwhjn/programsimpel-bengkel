<?php

namespace App\Http\Controllers;

use App\Models\Tjualnotad;
use App\Models\Tjualnotah;
use App\Models\Tlaporanstok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JualNotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $items = Tjualnotah::query()
                ->with([
                    'customer',
                    'workorder',
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

        return view('jualnota.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('jualnota.form');
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
            $prefix = 'J/' . Carbon::parse($request->input('tanggal'))->format('y/m/');
            $kode = Tjualnotah::generateKode($prefix);

            $request->merge([
                'tanggal' => $tanggal,
                'kode' => $kode,
            ]);

            $item = new Tjualnotah($request->all());
            $item->save();

            foreach ($request->input('jualnota_detail', []) as $row) {
                $item_detail = new Tjualnotad($row);
                $item->jualnotaDetail()->save($item_detail);

                if(isset($row['barang']) && $row['barang']['is_jasa'] == 0) {
                    $stok = new Tlaporanstok([
                        'jualnotah_id' => $item->id,
                        'jualnotad_id' => $item_detail->id,
                        'gudang_id' => $item->gudang_id,
                        'barang_id' => $item_detail->barang_id,
                        'jumlah' => floatval($item_detail->jumlah) * -1,
                        'tanggal' => Carbon::now(),
                    ]);
                    $stok->save();
                }
            }

            $redirect_to = route('jualnota.show', $item);

            DB::commit();

            return response()->json([
                'redirect_to' => $redirect_to,
                '_all' => $request->all(),
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
        $item = $this->_show($id);

        return view('jualnota.form', compact('item'));
    }

    private function _show($id)
    {
        $item = Tjualnotah::query()
            ->with([
                'gudang',
                'customer',
                'workorder',
                'jualnotaDetail.barang',
            ])
            ->find($id);

        return $item;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->_show($id);

        return view('jualnota.form', compact('item'));
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
            $item = Tjualnotah::query()->find($id);
            $item->update($request->all());

            $item->jualnotaDetail()->delete();
            Tlaporanstok::query()
                ->where('jualnotah_id', $id)
                ->delete();

            foreach ($request->input('jualnota_detail', []) as $row) {
                $item_detail = new Tjualnotad($row);
                $item->jualnotaDetail()->save($item_detail);

                if(isset($row['barang']) && $row['barang']['is_jasa'] == 0) {
                    $stok = new Tlaporanstok([
                        'jualnotah_id' => $item->id,
                        'jualnotad_id' => $item_detail->id,
                        'gudang_id' => $item->gudang_id,
                        'barang_id' => $item_detail->barang_id,
                        'jumlah' => floatval($item_detail->jumlah) * -1,
                        'tanggal' => Carbon::now(),
                    ]);
                    $stok->save();
                }
            }

            $redirect_to = route('jualnota.show', $item);

            DB::commit();

            return response()->json([
                'redirect_to' => $redirect_to,
                '_all' => $request->all(),
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

    // ukuran kertas pakai `point`, search google mm to point
    // pakai:
    // https://cssunitconverter.vercel.app/mm-to-pt
    public function pracetak($id)
    {
        $item = $this->_show($id);

        // 215, 140
        $pdf = new \Mpdf\Mpdf([
            'format' => [215, 140],
        ]);

        $html_output = view('jualnota.pracetak', compact('item'));
        $pdf->WriteHTML($html_output);
        $pdf->Output();

        // return $pdf->stream();
        // return view('jualnota.pracetak', [
        //     'item' => $item,
        // ]);
    }
}
