@extends('ps::layouts.admin')

@section('pageTitle', 'Laporan Ringkasan Stok')

@section('content')
<div class="box" ng-controller="PageController">
    <div class="box-header with-border">
        <h3 class="box-title">@yield('pageTitle')</h3>
    </div>
    <div class="box-body">
        <table class="table table-hover" datatable dt-options="dtOptions" dt-columns="dtColumns" dt-instance="dtInstance"></table>
    </div>
</div>


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
                    url: route('laporan_ringkasan_stok'),
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
                DTColumnBuilder.newColumn('barang').withTitle('Barang'),
                DTColumnBuilder.newColumn('masuk').withTitle('Masuk')
                    .withOption('class', 'dt-right')
                    .withOption('width', 90),
                DTColumnBuilder.newColumn('keluar').withTitle('Keluar')
                    .withOption('class', 'dt-right')
                    .withOption('width', 90),
                DTColumnBuilder.newColumn('saldo').withTitle('Saldo')
                    .withOption('class', 'dt-right')
                    .withOption('width', 90),
            ];
        }
    })();
</script>
@endpush

@endsection
