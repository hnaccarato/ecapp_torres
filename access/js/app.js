var host = 'http://'+document.location.hostname+'/barilocheturismo.gob.ar/admin/';

var myApp = angular.module('myApp', []);
myApp.controller('get_hoteles', function ($scope, $http, $filter,data_factory){

    $scope.hoteles = '';
	$scope.filtro = '';

	/*
    $http.get(host+'obtener_json_hoteles').success(function(data){
        $scope.hoteles = data;
    });*/

    $scope.order = function(predicate) {
        $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
        $scope.predicate = predicate;
    },

	$scope.getData = function(filtro){
		data_factory.getData($scope.filtro).success(function(respuesta){
			$scope.hoteles = data;
		});
	}	

})
.factory("data_factory",['$http',function($http,CONFIG){

	return{
		geUsers: function(filtro){
			return $http({
				url:"http://192.168.1.3/barilocheturismo.gob.ar/admin/obtener_json_hoteles",
				method: 'POST',
                data: 'filtro='+filtro,
                headers: {'Content-Type':'application/x-www-form-urlencoded'}
				})
			}
		}

}])
