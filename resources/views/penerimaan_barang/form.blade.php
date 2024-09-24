@extends('ps::layouts.admin')

@section('pageTitle', 'Penerimaan Barang')

@section('content')
<div class="box" ng-controller="PageController">
    <div class="box-header with-border">
        <h3 class="box-title">@yield('pageTitle')</h3>
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
                            <label>Gudang</label>
                            <x-input-search search-options="browseGudang" name="gudang_nama" ng-model="formfield.gudang.nama" validator="required" />
                        </div>
                    </div>
                </div>

                <x-grid-form :grid-options="'gridform1'"/>
            </fieldset>
        </div>
        <div class="box-footer">
            @if($action_method != 'show')
                <button type="submit" class="btn btn-primary">Save</button>
            @endif
            <a href="{{ route('penerimaan_barang.index') }}" class="btn btn-default">Back</a>
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
                    rowEntity.barang_id = item.value.id;
                    // rowEntity.jumlah = 1;
                    // rowEntity.harga = item.value.harga_jual;
                },
            };

            $scope.browseGudang = {
                items: function (viewValue) {
                    console.log(viewValue)

                    return $http.get('/api/browse/gudang', {
                        params: {
                            keyword: viewValue,
                        },
                    });
                },
                onSelect: function (item) {
                    console.log(item)

                    $scope.formfield.gudang = item.value;
                    $scope.formfield.gudang_id = item.value.id;
                },
            };

            $scope.gridform1 = {
                data: [{}],
                columnDefs: [
                    {
                        name: 'barang_nama',
                        field: 'barang.nama',
                        displayName: 'Barang',
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
                        name: 'jumlah_selisih',
                        field: 'jumlah_selisih',
                        cellFilter: 'number',
                        cellClass: 'text-right',
                        validators: {
                            required: true
                        },
                        width: 90,
                        enableCellEdit: false,
                        visible: false,
                    },
                    {
                        name: 'keterangan',
                        field: 'keterangan',
                        width: 600,
                    },
                ],
                afterCellEdit: function (rowEntity, colDef, newValue, oldValue) {
                    console.log('jualnota afterCellEdit')
                    console.log(rowEntity)

                    rowEntity.jumlah_awal = 0;
                    rowEntity.jumlah_selisih = parseFloat(rowEntity.jumlah) - parseFloat(rowEntity.jumlah_awal);
                }
            };

            if (window.item && Object.keys(window.item).length > 0) {
                $scope.formfield = item;

                $scope.gridform1.data = item.stok_opname_detail;
            }

            $scope.save = function (formCtrl, evt) {
                var btnSubmit = angular.element(evt.currentTarget).find('button[type="submit"]');
                btnSubmit.prop('disabled', true);
                evt.preventDefault();

                $scope.formfield.jenis = 'Penerimaan Barang';
                $scope.formfield.stok_opname_detail = $scope.gridform1.data;

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
