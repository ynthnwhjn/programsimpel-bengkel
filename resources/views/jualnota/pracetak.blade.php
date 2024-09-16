@extends('ps::layouts.pracetak')

@section('pageTitle', 'Penjualan')

@section('content')
<h3 class="text-center">{{ $item->kode }}</h3>
<div>{{ $item->tanggal }}</div>
<div>{{ $item->customer->nama }}</div>

<table>
    <tbody>
        @foreach($item->jualnotaDetail as $index => $row)
        <tr>
            <td>{{ ($index + 1) }}</td>
            <td>{{ $row->barang->nama }}</td>
            <td class="text-right">{{ $row->jumlah }}</td>
            <td class="text-right">{{ $row->harga }}</td>
            <td class="text-right">{{ $row->subtotal }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
