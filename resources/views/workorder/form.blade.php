@extends('ps::layouts.admin')

@section('pageTitle', 'Perintah kerja')

@section('content')
<div class="box" ng-controller="PageController">
    <div class="box-header with-border">
        <h3 class="box-title">
            @yield('pageTitle')
            @isset($item->jualnota)
                <div class="label bg-green" style="margin-left: 5px;">
                    <i class="fa fa-check-circle"></i>
                    <span style="margin-left: 5px;">Approved</span>
                </div>
            @endisset
        </h3>

        <div class="btn-group">
            @if(!isset($item->jualnota) && $action_method != 'create')
                <button type="button" ng-click="approveWorkOrder($event)" class="btn btn-success"><i class="fa fa-check-circle"></i> Approve</button>
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
                            <label>Montir</label>
                            <x-input-search search-options="browseMontir" name="montir_nama" ng-model="formfield.montir.nama" validator="required" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Customer</label>
                            <x-input-search search-options="browseCustomer" name="customer_nama" ng-model="formfield.customer.nama" validator="required" />
                        </div>

                        <div class="form-group">
                            <label>Nopol</label>
                            <input type="text" class="form-control" name="nopol" ng-model="formfield.nopol" validator="required">
                        </div>

                        <div class="form-group">
                            <label>Jenis</label>
                            <select class="form-control" name="jenis" ng-model="formfield.jenis" validator="required">
                                <option value="Servis">Servis</option>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="box-footer">
            @if($action_method != 'show')
                <button type="submit" class="btn btn-primary">Save</button>
            @endif
            <a href="{{ route('workorder.index') }}" class="btn btn-default">Back</a>
        </div>
    </form>
</div>

@if(isset($item))
@javascript('item', $item)
@endif

<script type="text/ng-template" id="modal_confirm_finish_workorder.html">
    <div class="modal-header">
        <button type="button" class="close" ng-click="$dismiss('cancel')" aria-label="Close">&times;</button>
        <h4 class="modal-title">Confirm</h4>
    </div>
    <form name="frm" ng-submit="submit(frm, $event)">
        <div class="modal-body">
            Anda yakin?
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> OK</button>
            <button type="button" class="btn btn-default" ng-click="$dismiss('cancel')"><i class="fa fa-times"></i> Cancel</button>
        </div>
    </form>
</script>

@push('scripts')
<script type="text/javascript">
    (function () {
        'use strict';

        angular.module('programsimpel').controller('PageController', PageController);

        function PageController($scope, $http, $validation, $gridFormValidation, $uibModal, $q) {
            $scope.formfield = {};

            $scope.approveWorkOrder = function (evt) {
                var btnConfirm = angular.element(evt.currentTarget);
                btnConfirm.prop('disabled', true);

                var modalInstance = $uibModal.open({
                    templateUrl: 'modal_confirm_finish_workorder.html',
                    size: 'sm',
                    backdrop: 'static',
                    scope: $scope,
                    controller: function ($scope, $uibModalInstance) {
                        $scope.submit = function (formCtrl, evt) {
                            var btnSubmit = angular.element(evt.currentTarget).find('button[type="submit"]');
                            btnSubmit.prop('disabled', true);

                            $http.post(route('workorder.approve'), $scope.formfield)
                                .then(function (response) {
                                    $uibModalInstance.close(response.data);

                                    btnSubmit.prop('disabled', false);
                                }, function () {
                                    btnSubmit.prop('disabled', false);
                                });
                        }
                    }
                });

                modalInstance.result.then(function (responseData) {
                    if (responseData.redirect_to) {
                        window.location.href = responseData.redirect_to;
                    }

                    btnConfirm.prop('disabled', false);
                }, function () {
                    btnConfirm.prop('disabled', false);
                });
            }

            $scope.browseMontir = {
                items: function (viewValue) {
                    console.log(viewValue)

                    return $http.get('/api/browse/montir', {
                        params: {
                            keyword: viewValue,
                        },
                    });
                },
                onSelect: function (item) {
                    $scope.formfield.montir = item.value;
                    $scope.formfield.montir_id = item.value.id;
                },
            };

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

            if (window.item) {
                $scope.formfield = item;
            }

            $scope.save = function (formCtrl, evt) {
                var btnSubmit = angular.element(evt.currentTarget).find('button[type="submit"]');
                btnSubmit.prop('disabled', true);
                evt.preventDefault();

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

@endsection
