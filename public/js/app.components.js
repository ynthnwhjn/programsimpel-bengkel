(function () {
    'use strict';

    angular.module('programsimpel')
        .factory('$gridFormValidation', function ($q, uiGridValidateService) {
            return {
                validate: function (gridApi) {
                    var deferred = $q.defer();
                    var errors = {};

                    console.log('gridFormValidation.validate...')
                    console.log(gridApi)

                    deferred.promise.success = function (fn) {
                        deferred.promise.then(function (value) {
                            fn(value);
                        });
                        return deferred.promise;
                    };

                    deferred.promise.error = function (fn) {
                        deferred.promise.then(null, function (value) {
                            fn(value);
                        });
                        return deferred.promise;
                    };

                    angular.forEach(gridApi.grid.renderContainers.body.visibleRowCache, function (row, rowIndex) {
                        angular.forEach(gridApi.grid.renderContainers.body.visibleColumnCache, function (col, colIndex) {
                            if (col.colDef.validators) {
                                var cellValue = gridApi.grid.getCellValue(row, col);

                                uiGridValidateService.runValidators(row.entity, col.colDef, cellValue, NaN, gridApi.grid);
                                console.log(cellValue)

                                if (!cellValue) {
                                    uiGridValidateService.setInvalid(row.entity, col.colDef);
                                    console.log('setInvalid...')

                                    if ('required' in col.colDef.validators) {
                                        uiGridValidateService.setError(row.entity, col.colDef, 'required');
                                    }
                                }

                                var isInvalid = uiGridValidateService.isInvalid(row.entity, col.colDef);
                                console.log('isInvalid...', isInvalid)

                                if (isInvalid) {
                                    errors[col.name] = uiGridValidateService.getErrorMessages(row.entity, col.colDef);
                                }
                            }
                        });
                    });

                    if (Object.keys(errors).length) {
                        deferred.reject('error');
                    }
                    else {
                        deferred.resolve('success');
                    }

                    return deferred.promise;
                }
            }
        })
        .directive('gridForm', function ($parse, $timeout, $gridFormValidation, toastr) {
            return {
                link: function ($scope, elem, attrs) {
                    $scope.gridFormOptions = $parse(attrs.gridForm)($scope);

                    function handleWindowResize() {
                        $timeout(function () {
                            $scope.gridApi.core.handleWindowResize();
                        }, 500);
                    }

                    angular.forEach($scope.gridFormOptions.columnDefs, function (column) {
                        column.cellTooltip = true;
                        column.headerTooltip = true;

                        if (column.validators !== undefined) {
                            column.cellTemplate = 'ui-grid/cellTitleValidator';

                            if (column.validators.required == true) {
                                column.headerCellClass = 'required';
                            }
                        }
                    });

                    var defaultOptions = {
                        enableColumnResizing: true,
                        enableSelectAll: true,
                        enableFullRowSelection: false,
                        multiSelect: true,
                        enableSorting: false,
                        enableColumnMenus: false,
                        onRegisterApi: function (gridApi) {
                            console.log(gridApi)
                            $scope.gridApi = gridApi;

                            $scope.gridFormOptions.gridApi = gridApi;

                            angular.element('.sidebar-toggle').on('click', function () {
                                console.log('sidebar-toggle click')
                                handleWindowResize();
                            });

                            handleWindowResize();

                            if(gridApi.edit) {
                                gridApi.edit.on.afterCellEdit($scope, function (rowEntity, colDef, newValue, oldValue) {
                                    $scope.gridFormOptions.afterCellEdit(rowEntity, colDef, newValue, oldValue);
                                });
                            }
                        }
                    };

                    $scope.gridOptions = angular.extend(defaultOptions, $scope.gridFormOptions);

                    $scope.actionAddGridRow = function (evt) {
                        $gridFormValidation.validate($scope.gridApi)
                            .success(function () {
                                $scope.gridOptions.data.push({});
                            })
                            .error(function () {
                                toastr.error('Detail wajib diisi');
                            });
                    }

                    $scope.actionDeleteGridRow = function (evt) {
                        var selectedRows = $scope.gridApi.selection.getSelectedRows();

                        if (selectedRows.length) {
                            for (var a = 0; a < selectedRows.length; a++) {
                                var selectedIndex = $scope.gridOptions.data.indexOf(selectedRows[a]);
                                $scope.gridOptions.data.splice(selectedIndex, 1);
                            }
                        }
                    }

                    console.log($scope.gridOptions)
                }
            }
        })
})();

(function () {
    'use strict';

    angular.module('programsimpel')
        .directive('inputSearch', function ($parse) {
            return {
                scope: true,
                link: function ($scope, elem, attrs) {
                    console.log(attrs)

                    var searchOptions = $parse(attrs.inputSearch)($scope);
                    console.log(searchOptions)

                    $scope.getItems = function (viewValue) {
                        return searchOptions.items(viewValue)
                            .then(function (response) {
                                return response.data.items;
                            });
                    }

                    $scope.onSelect = function ($item, $model, $label, $event) {
                        console.log($item)
                        if(searchOptions.onSelect) {
                            searchOptions.onSelect($item);
                        }
                    }
                }
            }
        })

        .directive('gridFormInputSearch', function ($parse, $timeout, uiGridEditConstants) {
            return {
                scope: true,
                require: ['?^uiGrid', '?^uiGridRenderContainer'],
                link: function ($scope, elem, attrs) {
                    var searchOptions = $parse(attrs.gridFormInputSearch)($scope);

                    console.log('gridFormInputSearch...')
                    var elemInput = elem.find('input');

                    $scope.$on(uiGridEditConstants.events.BEGIN_CELL_EDIT, function () {
                        $timeout(function () {
                            elemInput.focus();
                        });
                    });

                    $scope.getItems = function (viewValue) {
                        return searchOptions.items(viewValue)
                            .then(function (response) {
                                return response.data.items;
                            });
                    }

                    $scope.onSelect = function ($item, $model, $label, $event) {
                        console.log($item)
                        if (searchOptions.onSelect) {
                            searchOptions.onSelect($item, $scope.row.entity);
                        }

                        $scope.$emit(uiGridEditConstants.events.END_CELL_EDIT);
                    }

                    $scope.$on('$destroy', function unbindEvents() {
                        elem.off();
                    });
                }
            }
        })

})();
