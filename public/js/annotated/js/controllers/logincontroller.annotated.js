angular.module( 'bmuApp.login', [
  'ui.router',
  'angular-storage',
  'angular-jwt'
]).controller( 'logincontroller', ["$scope", "$http", "store", "$state", "$stateParams", "URL_BASE", "UserService", "$rootScope", function LoginController( $scope, $http, store, $state, $stateParams, URL_BASE, UserService, $rootScope) {

  $scope.user = {};

  $scope.loginMessage = $stateParams.message;


  $scope.login = function() {
    if(!UserService.login($scope.user)) {
      $scope.loginAlert = "Login failed! Please make sure email and password are correct.";
    }
  }

}]);

angular.module('bmuApp.login').controller( 'logoutcontroller', ["$scope", "$http", "store", "$state", "URL_BASE", "UserService", "$rootScope", function LogoutController( $scope, $http, store, $state, URL_BASE, UserService, $rootScope) {
  UserService.logout();
}]);

angular.module('bmuApp.login').directive('autoFillSync', ["$timeout", function($timeout) {
   return {
      require: 'ngModel',
      link: function(scope, elem, attrs, ngModel) {
          var origVal = elem.val();
          $timeout(function () {
              var newVal = elem.val();
              if(ngModel.$pristine && origVal !== newVal) {
                  ngModel.$setViewValue(newVal);
              }
          }, 500);
      }
   }
}]);

angular.module('bmuApp.login').controller( 'forgotcontroller', ["$scope", "$http", "store", "$state", "URL_BASE", "UserService", "$rootScope", function LogoutController( $scope, $http, store, $state, URL_BASE, UserService, $rootScope) {

}]);


angular.module('bmuApp.login').run(["$rootScope", "UserService", "$state", function($rootScope, UserService, $state){
  UserService.getUser();
  $rootScope.$watch( UserService.isLoggedIn, function (isLoggedIn) {
    UserService.getUser();
    $rootScope.currentUser = UserService.currentUser();
    $rootScope.isLoggedIn = isLoggedIn;
    if($rootScope.isLoggedIn) {
      //console.log($state);
    }
  });
}]);
