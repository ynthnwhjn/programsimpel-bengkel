@extends('ps::layouts.pracetak')

@section('pageTitle', 'Penjualan')

@section('content')

@php
$per_page = 10;
$total_rows = $per_page * ceil(count($item->jualnotaDetail) / $per_page);
@endphp

@push('styles')
<style>
    @page {
        margin-top: 30mm;;
        header: html_pageheader1;
    }
</style>
@endpush

<htmlpageheader name="pageheader1">
    <div style="width: 65%; float: left;">
        <h3>Nota Penjualan</h3>
    </div>

    <div style="width: 35%; float: left;">
        <table>
            <tbody>
                <tr>
                    <td style="width: 100px;">No. Nota</td>
                    <td style="width: 5px;">:</td>
                    <td>{{ $item->kode }}</td>
                </tr>
                <tr>
                    <td style="width: 100px;">Tanggal</td>
                    <td style="width: 5px;">:</td>
                    <td>{{ $item->tanggal }}</td>
                </tr>
                <tr>
                    <td style="width: 100px;">Customer</td>
                    <td style="width: 5px;">:</td>
                    <td>{{ $item->customer->nama }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</htmlpageheader>

<table class="table-bordered">
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Nama Barang</th>
            <th style="width: 60px;">Jumlah</th>
            <th style="width: 90px;">Harga</th>
            <th style="width: 90px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < $total_rows; $i++)

        @php
            $row = isset($item->jualnotaDetail[$i]) ? $item->jualnotaDetail[$i] : false;
        @endphp

        @if($row)
            <tr>
                <td class="text-right">{{ ($i + 1) }}</td>
                <td>{{ $row->barang->nama }}</td>
                <td class="text-right">{{ floatval($row->jumlah) }}</td>
                <td class="text-right">{{ floatval($row->harga) }}</td>
                <td class="text-right">{{ floatval($row->subtotal) }}</td>
            </tr>
        @else
            <tr class="{{ (($i + 1) % $per_page) == 0 ? 'border-bottom' : '' }}">
                <td class="text-right">
                    <!-- {{ $i + 1 }} -->
                </td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endif
        @endfor
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"></td>
            <td class="border-left border-right text-right">Subtotal</td>
            <td class="border-left border-right text-right">{{ floatval($item->subtotal) }}</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td class="border-left border-right border-bottom text-right">Total</td>
            <td class="border-left border-right border-bottom text-right">{{ floatval($item->total) }}</td>
        </tr>
    </tfoot>
</table>

@endsection
