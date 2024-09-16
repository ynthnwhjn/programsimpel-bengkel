<?php

namespace App\Http\Controllers;

use App\Models\Tjualnotad;
use App\Models\Tjualnotah;
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
        $item = new Tjualnotah();

        return view('jualnota.form', compact('item'));
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
            }

            $redirect_to = route('jualnota.show', $item);

            DB::commit();

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
        $item = Tjualnotah::query()
            ->with([
                'customer',
                'workorder',
                'jualnotaDetail.barang',
            ])
            ->find($id);

        return view('jualnota.form', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Tjualnotah::query()
            ->with([
                'customer',
                'workorder',
                'jualnotaDetail.barang',
            ])
            ->find($id);

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
            foreach ($request->input('jualnota_detail', []) as $row) {
                $item_detail = new Tjualnotad($row);
                $item->jualnotaDetail()->save($item_detail);
            }

            $redirect_to = route('jualnota.show', $item);

            DB::commit();

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
