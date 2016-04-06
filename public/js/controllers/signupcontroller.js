angular.module( 'bmuApp.signup', [
  'ui.router',
  'angular-storage',
  'ngFileUpload',
])
.controller( "signupcontroller", function SignupController( $scope, $state, UserService, Upload, $rootScope) {


  $scope.user = {};
  $scope.imageError = false;
  $scope.posting = false;

  $scope.errorString = "";

  $scope.error = function(message) {
    var str = (JSON.stringify(message));
    $scope.errorString = str.replace(/^"(.+(?="$))"$/, '$1');
  }


  $scope.uploadPic = function(dataUrl) {
    console.log(dataUrl);
    $scope.imageError = false;

    user = $scope.user;

    $scope.data = {
      "email":          user.email,
      "first-name":     user.firstName,
      "last-name":      user.lastName,
      "username":       user.username,
      "password-init":  user.password,
      "school-id":      user.schoolId,
      "grad-class":     user.gradClass,
     };
     $scope.posting = true;

     Upload.upload({
       url: 'https://blackmarketu.com/api/create-account',
       data: {
         "profile-image":  dataUrl,
         "email":          user.email,
         "first-name":     user.firstName,
         "last-name":      user.lastName,
         "username":       user.username,
         "password-init":  user.password,
         "password-check": user.passwordCheck,
         "school-id":      user.schoolId,
         "grad-class":     user.gradClass,
       }
     }).then(function (response) {
       $scope.posting = false;
       $state.go("loginState", { message: "Please check your email to activate your account!" });
     }, function (response) {
       if (response.status > 0)
       $scope.errorMsg = response.status + ': ' + JSON.stringify(response.data);
       $scope.posting = false;
       console.log($scope.errorMsg.error);
     }, function (evt) {
       //file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
     });
   };
 });


 angular.module('bmuApp.login').run(function($rootScope, UserService, $state){
   $rootScope.$watch( UserService.isLoggedIn, function (isLoggedIn) {
     $rootScope.currentUser = UserService.currentUser();
     $rootScope.isLoggedIn = isLoggedIn;
     if($rootScope.isLoggedIn) {
       if($state.current.name == "loginState" || $state.current.name == "signupState")
       $state.go("postState");
     }
 });

 });

 //check for .edu email
 angular.module('bmuApp').directive('eduEmail', function() {

    function checkEdu(value) {
     console.log(value);
     if(value.indexOf("@") > -1 && value.indexOf('.') != -1) {
       str = value.split("@");
       str2 = str[1].split(".");
       if (str2[str2.length-1] == "edu") {
         return true;
       }

       else return false;
     }

     else return false;

   }

   return {
     require: 'ngModel',
     restrict: '',
     link: function(scope, elm, attrs, ctrl) {
       // only apply the validator if ngModel is present and Angular has added the email validator
       if (ctrl && ctrl.$validators.email) {

         // this will overwrite the default Angular email validator
         ctrl.$validators.email = function(modelValue) {
           return ctrl.$isEmpty(modelValue) || checkEdu(modelValue);
         };
       }
     }
   };

 });

 angular.module('bmuApp').directive('username', function($http, $q) {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$asyncValidators.username = function(modelValue, viewValue) {
                return $http.post('https://www.blackmarketu.com/api/verify/username', {username: viewValue}).then(
                    function(response) {
                      console.log(response);
                        if (response.data === "1") {
                              return $q.reject(response.data.errorMessage);
                        }
                        return true;
                    }
                );
            };
        }
    };
});

angular.module('bmuApp').directive('email', function($http, $q) {
   return {
       require: 'ngModel',
       link: function(scope, element, attrs, ngModel) {
           ngModel.$asyncValidators.email = function(modelValue, viewValue) {
               return $http.post('https://www.blackmarketu.com/api/verify/email', {email: viewValue}).then(
                   function(response) {
                       if (response.data === "1") {
                             return $q.reject(response.data.errorMessage);
                       }
                       return true;
                   }
               );
           };
       }
   };
});

angular.module('bmuApp').directive('equals', function() {
  return {
    restrict: 'A', // only activate on element attribute
    require: '?ngModel', // get a hold of NgModelController
    link: function(scope, elem, attrs, ngModel) {
      if(!ngModel) return; // do nothing if no ng-model

      // watch own value and re-validate on change
      scope.$watch(attrs.ngModel, function() {
        validate();
      });

      // observe the other value and re-validate on change
      attrs.$observe('equals', function (val) {
        validate();
      });

      var validate = function() {
        // values
        var val1 = ngModel.$viewValue;
        var val2 = attrs.equals;

        // set validity
        ngModel.$setValidity('equals', ! val1 || ! val2 || val1 === val2);
      };
    }
  }
});
