@extends('ps::layouts.admin')

@section('pageTitle', 'Penerimaan Barang')

@section('content')
<div class="box" ng-controller="PageController">
    <div class="box-header with-border">
        <h3 class="box-title">@yield('pageTitle')</h3>

        <div class="btn-group">
            <a href="{{ route('penerimaan_barang.create') }}" class="btn btn-default">Create</a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-hover" datatable dt-options="dtOptions" dt-columns="dtColumns" dt-instance="dtInstance"></table>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    (function () {
        'use strict';

        angular.module('programsimpel').controller('PageController', PageController);

        function PageController($scope, $uibModal, $http, $validation, DTOptionsBuilder, DTColumnBuilder) {
            $scope.filter = {};
            $scope.dtInstance = {};

            $scope.dtOptions = DTOptionsBuilder.newOptions()
                .withOption('ajax', {
                    // headers: {
                    //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    // },
                    dataSrc: 'data',
                    url: route('penerimaan_barang.index'),
                    // data: function (d) {
                    //     d = $scope.filter;
                    //     console.log(d)
                    //     console.log($scope.filter)

                    //     return d;
                    // }
                })
                .withOption('processing', true)
                .withOption('serverSide', true);

            $scope.dtColumns = [
                DTColumnBuilder.newColumn(null)
                    .withTitle('#')
                    .withOption('width', 40)
                    .notSortable()
                    .renderWith(function (data, type, row, meta) {
                        return meta.row + 1;
                    }),
                DTColumnBuilder.newColumn('kode').withTitle('Kode'),
                DTColumnBuilder.newColumn('tanggal').withTitle('Tanggal'),
                DTColumnBuilder.newColumn('gudang').withTitle('Gudang')
                    .renderWith(function (data, type, row, meta) {
                        return data ? data.nama : '';
                    }),
                DTColumnBuilder.newColumn(null).withTitle('<i class="fa fa-bars"></i>')
                    .withOption('width', 90)
                    .withOption('class', 'dt-center')
                    .renderWith(function (data, type, row, meta) {
                        console.log(row)

                        return '<a href="'+ route('penerimaan_barang.edit', row) +'">Edit</a>';
                    }),
            ];
        }
    })();
</script>
@endpush
