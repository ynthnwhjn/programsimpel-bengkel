@extends('ps::layouts.admin')

@section('pageTitle', 'Barang')

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
                            <label>Nama</label>
                            <input type="text" class="form-control" name="nama" ng-model="formfield.nama">
                        </div>

                        <div class="form-group">
                            <label>Harga beli</label>
                            <input type="text" class="form-control" name="harga_beli" ng-model="formfield.harga_beli">
                        </div>

                        <div class="form-group">
                            <label>Harga jual</label>
                            <input type="text" class="form-control" name="harga_jual" ng-model="formfield.harga_jual">
                        </div>

                        <div class="form-group">
                            <label>Jasa?</label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_jasa" ng-model="formfield.is_jasa" ng-true-value="1" ng-false-value="0">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="box-footer">
            @if($action_method != 'show')
                <button type="submit" class="btn btn-primary">Save</button>
            @endif
            <a href="{{ route('barang.index') }}" class="btn btn-default">Back</a>
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
