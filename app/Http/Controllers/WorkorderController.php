<?php

namespace App\Http\Controllers;

use App\Models\Tjualnotah;
use App\Models\Tworkorderh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class WorkorderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax()) {
            $items = Tworkorderh::query()
                ->with([
                    'montir',
                    'customer',
                    'jualnota',
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

        return view('workorder.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new Tworkorderh();

        return view('workorder.form', compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tanggal = Carbon::parse($request->input('tanggal'))->toDateString();
        $prefix = 'WO/' . Carbon::parse($request->input('tanggal'))->format('y/m/');
        $kode = Tworkorderh::generateKode($prefix);

        $request->merge([
            'tanggal' => $tanggal,
            'kode' => $kode,
        ]);

        $item = new Tworkorderh($request->all());
        $item->save();

        $redirect_to = route('workorder.show', $item);

        return response()->json([
            'redirect_to' => $redirect_to,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Tworkorderh::query()
            ->with([
                'montir',
                'customer',
                'jualnota',
            ])
            ->find($id);

        return view('workorder.form', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Tworkorderh::query()
            ->with([
                'montir',
                'customer',
                'jualnota',
            ])
            ->find($id);

        return view('workorder.form', compact('item'));
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
        //
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

    public function approve(Request $request)
    {
        $workorder = Tworkorderh::query()
            ->findOrFail($request->input('id'));

        $prefix = 'J/' . Carbon::now()->format('y/m/');
        $jualnota_kode = Tjualnotah::generateKode($prefix);
        $jualnota = new Tjualnotah([
            'workorderh_id' => $workorder->id,
            'customer_id' => $workorder->customer_id,
            'kode' => $jualnota_kode,
            'tanggal' => Carbon::now(),
        ]);
        $jualnota->save();

        $redirect_to = route('jualnota.edit', $jualnota);

        return response()->json([
            '_all' => $request->all(),
            'redirect_to' => $redirect_to,
        ]);
    }
}
