angular.module( 'bmuApp.login', [
  'ui.router',
  'angular-storage',
  'angular-jwt'
]).controller( 'logincontroller', function LoginController( $scope, $http, store, $state, $stateParams, URL_BASE, UserService, $rootScope) {

  $scope.user = {};
  $scope.data = {};

  $scope.loginMessage = $stateParams.message;


  $scope.login = function() {
    if($scope.user.email.indexOf("@") < 1) {
      $scope.data.username = $scope.user.email;
    }

    else {
      delete $scope.data.username;
      $scope.data.email = $scope.user.email;
    }

    $scope.data.password = $scope.user.password;
    console.log($scope.data);

    UserService.login($scope.data).then(function(d) {
      if(d === false) {
        $scope.loginAlert = "Login failed! Please make sure email and password are correct.";
        return;
      }
    });
  }

});

angular.module('bmuApp.login').controller( 'resendcontroller', function LogoutController( $scope, $http, store, $state, URL_BASE, UserService, $rootScope) {
  $scope.user = {};
  $scope.error = false;

  $scope.resend = function() {
    UserService.resend($scope.user.email).then(function(d){
      if(d === false) {
        $scope.error = true;
      }
    });
  }
});

angular.module('bmuApp.login').controller( 'logoutcontroller', function LogoutController( $scope, $http, store, $state, URL_BASE, UserService, $rootScope) {
  UserService.logout();
});

angular.module('bmuApp.login').directive('autoFillSync', function($timeout) {
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
});

angular.module('bmuApp.login').controller( 'forgotcontroller', function LogoutController( $scope, $http, store, $state, URL_BASE, UserService, $rootScope) {

  $scope.notVerified = true;

  $scope.sendRecoveryEmail = function(email) {
    $scope.loading = true;
    $http({
      method: "POST",
      url:    "https://www.blackmarketu.com/api/forgot/password",
      data:   {
        email: email
      }
    }).then(
      function success(data, status, headers, config) {
        $scope.loading = false;
        $scope.error = false;
        $scope.notVerified = false;
      }, function error(response) {
        $scope.loading = false;
        $scope.error = true;
      }
    );
  }

});

angular.module('bmuApp.login').controller( 'recovercontroller', function LogoutController( $scope, $http, store, $state, $stateParams, URL_BASE, UserService, $rootScope) {

  $scope.allowChange = false;
  $scope.passerror =  false;
  $scope.user = {};

  $scope.checkCode = function() {
    $http({
      method: "GET",
      url:    "https://www.blackmarketu.com/api/forgot/password/"+$stateParams.code,
    }).then(
      function success(data, status, headers, config) {
        $scope.allowChange = true;
      }, function error(response) {
        $scope.allowChange = false;
        $scope.error = true;
      }
    );
  }

  $scope.changePassword = function() {
    $scope.loading = true;
    $http({
      method: "POST",
      url:    "https://www.blackmarketu.com/api/forgot/password/recover",
      data: {
        password:       $scope.user.password,
        passwordCheck:  $scope.user.passwordCheck,
        code:           $stateParams.code
      }
    }).then(
      function success(data, status, headers, config) {
        $scope.loading = false;
        $state.go("loginState",{message: "Your account password has been reset!"})
      }, function error(response) {
        $scope.loading = false;
        $scope.passerror = true;
      }
    );
  }

  //run check code on load
  $scope.checkCode();

});


angular.module('bmuApp.login').run(function($rootScope, UserService, $state){
  UserService.getUser();
  $rootScope.$watch( UserService.isLoggedIn, function (isLoggedIn) {
    UserService.getUser();
    $rootScope.currentUser = UserService.currentUser();
    $rootScope.isLoggedIn = isLoggedIn;
    if($rootScope.isLoggedIn) {
      //console.log($state);
    }
  });
});
