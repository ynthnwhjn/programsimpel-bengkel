@extends('ps::layouts.admin')

@section('pageTitle', 'Penjualan')

@section('content')
<div class="box" ng-controller="PageController">
    <div class="box-header with-border">
        <h3 class="box-title">@yield('pageTitle')</h3>

        <div class="btn-group">
            @if($action_method == 'show')
            <a href="{{ route('jualnota.pracetak', $item) }}" target="_blank" class="btn btn-default">
                <i class="fa fa-print"></i> Print
            </a>
            @endif
        </div>
    </div>
    <form name="frm" ng-submit="save(frm, $event)">
        <div class="box-body">
            <fieldset @if($action_method == 'show') disabled @endif>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode</label>
                            <input type="text" readonly placeholder="AUTO" class="form-control" name="kode" ng-model="formfield.kode">
                        </div>

                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="text" class="form-control" name="tanggal" ng-model="formfield.tanggal" datetimepicker="{'format': 'DD-MM-YYYY'}" validator="required">
                        </div>

                        <div class="form-group">
                            <label>Customer</label>
                            <x-input-search search-options="browseCustomer" name="customer_nama" ng-model="formfield.customer.nama" validator="required" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        @if($item->workorder)
                        <div class="form-group">
                            <label>Perintah Kerja</label>
                            <input type="text" readonly class="form-control" name="workorder_kode" ng-model="formfield.workorder.kode">
                        </div>
                        @endif
                    </div>
                </div>

                <x-grid-form :grid-options="'gridform1'"/>

                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Total</label>
                            <input type="text" readonly class="form-control" name="total" ng-model="formfield.total">
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="box-footer">
            @if($action_method != 'show')
                <button type="submit" class="btn btn-primary">Save</button>
            @endif
            <a href="{{ route('jualnota.index') }}" class="btn btn-default">Back</a>
        </div>
    </form>
</div>

@if(isset($item))
@javascript('item', $item)
@endif

@endsection

@push('scripts')
<script type="text/javascript">
    (function () {
        'use strict';

        angular.module('programsimpel').controller('PageController', PageController);

        function PageController($scope, $http, $validation, $gridFormValidation, $q) {
            $scope.formfield = {};

            function calculateHeader() {
                var subtotal = 0;
                angular.forEach($scope.gridform1.data, function (row) {
                    subtotal += parseFloat(row.subtotal);
                });

                $scope.formfield.subtotal = subtotal;
                $scope.formfield.total = subtotal;
            }

            $scope.browseCustomer = {
                items: function (viewValue) {
                    console.log(viewValue)

                    return $http.get('/api/browse/customer', {
                        params: {
                            keyword: viewValue,
                        },
                    });
                },
                onSelect: function (item) {
                    $scope.formfield.customer = item.value;
                    $scope.formfield.customer_id = item.value.id;
                },
            };

            $scope.browseBarang = {
                items: function (viewValue) {
                    console.log(viewValue)

                    return $http.get('/api/browse/barang', {
                        params: {
                            keyword: viewValue,
                        },
                    });
                },
                onSelect: function (item, rowEntity) {
                    console.log(item)

                    rowEntity.barang = item.value;
                    rowEntity.jumlah = 1;
                    rowEntity.harga = item.value.harga_jual;
                    rowEntity.barang_id = item.value.id;
                },
            };

            $scope.gridform1 = {
                data: [{}],
                columnDefs: [
                    {
                        name: 'barang_nama',
                        field: 'barang.nama',
                        displayName: 'Barang / Jasa',
                        editableCellTemplate: 'grid-form/input-search',
                        searchOptions: $scope.browseBarang,
                        validators: {
                            required: true
                        },
                        width: 400,
                    },
                    {
                        name: 'jumlah',
                        field: 'jumlah',
                        cellFilter: 'number',
                        cellClass: 'text-right',
                        validators: {
                            required: true
                        },
                        width: 90,
                    },
                    {
                        name: 'harga',
                        field: 'harga',
                        cellFilter: 'number',
                        cellClass: 'text-right',
                        validators: {
                            required: true
                        },
                        width: 90,
                    },
                    {
                        name: 'subtotal',
                        field: 'subtotal',
                        cellFilter: 'number',
                        cellClass: 'text-right',
                        validators: {
                            required: true
                        },
                        enableCellEdit: false,
                        width: 90,
                    },
                    {
                        name: 'keterangan',
                        field: 'keterangan',
                    },
                ],
                afterCellEdit: function (rowEntity, colDef, newValue, oldValue) {
                    console.log('jualnota afterCellEdit')
                    console.log(rowEntity)

                    rowEntity.subtotal = parseFloat(rowEntity.jumlah) * parseFloat(rowEntity.harga);
                    calculateHeader();
                }
            };

            if (window.item) {
                $scope.formfield = item;

                $scope.gridform1.data = item.jualnota_detail;
            }

            $scope.save = function (formCtrl, evt) {
                var btnSubmit = angular.element(evt.currentTarget).find('button[type="submit"]');
                btnSubmit.prop('disabled', true);
                evt.preventDefault();

                $scope.formfield.jualnota_detail = $scope.gridform1.data;

                $validation.validate(formCtrl)
                    .success(function () {
                        $http({
                            url: form_action,
                            method: form_method,
                            data: $scope.formfield,
                        })
                            .then(function name(response) {
                                console.log(response)

                                if (response.data.redirect_to) {
                                    window.location.href = response.data.redirect_to;
                                }
                            }, function (rejection) {
                                btnSubmit.prop('disabled', false);
                            });
                    })
                    .error(function () {
                        btnSubmit.prop('disabled', false);
                    });
            }
        }

    })();
</script>
@endpush
