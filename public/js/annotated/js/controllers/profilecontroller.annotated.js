angular.module( 'bmuApp.profile', [
  'ui.router',
  'angular-storage',
  'ngFileUpload',
])
.controller( "profilecontroller", ["$scope", "$http", "$state", "UserService", "Upload", "$rootScope", "$stateParams", "URL_BASE", "$timeout", "uiGmapGoogleMapApi", function ProfileController( $scope, $http, $state, UserService, Upload, $rootScope, $stateParams, URL_BASE, $timeout, uiGmapGoogleMapApi) {

  //Init update object

    var noMorePosts = false;
    $scope.hasPosts = false;
    $scope.currentPage = 1;
    $scope.posts = [];
    $scope.isBusy = false;
    $scope.updatePassword = {};
    $scope.newItem;
    $scope.tradelist = [];
    $scope.map = { center: { latitude: 44.473101, longitude:  -73.204124 }, zoom: 16, };
    $scope.options = {scrollwheel: false};
    $scope.coordsUpdates = 0;
    $scope.dynamicMoveCtr = 0;
    $scope.marker = {
       id: 0,
       coords: {
         latitude: 44.473101,
         longitude:  -73.204124
       },
       options: { draggable: false },
       events: {

       }
     };
     $scope.$watchCollection("marker.coords", function (newVal, oldVal) {
       if (_.isEqual(newVal, oldVal))
         return;
       $scope.coordsUpdates++;
     });
     $timeout(function () {
       $scope.marker.coords = {
         latitude: 44.473101,
         longitude:  -73.204124
       };
       $scope.dynamicMoveCtr++;
       $timeout(function () {
         $scope.marker.coords = {
           latitude: 44.473101,
           longitude:  -73.204124
         };
         $scope.dynamicMoveCtr++;
       }, 2000);
     }, 1000);

    $rootScope.$watch( UserService.currentUser, function (user) {
      $scope.currentUser = $rootScope.currentUser;
      $scope.loadMorePosts();
    });

    uiGmapGoogleMapApi.then(function (map) {
        $timeout(function () {
            $scope.showMap = true;
        }, 100);
    });

    /*****IMAGES LOADED******/
    $scope.imgLoadedEvents = {

        always: function(instance) {

        },

        done: function(instance) {
            angular.element(instance.elements[0]).addClass('loaded-ed animated fadeIn');
            $rootScope.$broadcast('masonry.reload');
        },

        fail: function(instance) {
            // Do stuff
        }

    };

    $scope.getTradeList = function() {
      $http({
        url: 'https://'+URL_BASE+'api/tradelist',
        method: "GET"
      }).then(function(data) {
        if(data.data.tradelists) {
          $scope.tradelist = data.data.tradelists;
        }
      }).then(function(error) {
        console.log(error);
      });
    }

    $scope.getTradeList();

    $scope.addToTradeList = function() {
      $http({
        url: 'https://'+URL_BASE+'/api/tradelist',
        method: 'POST',
        data: {
          "item": $scope.newItem
        }
      }).then(function(data) {
        console.log(data.data);
        $scope.tradelist.push(data.data);
        $scope.newItem = "";
      }).then(function(error) {
        console.log(error);
      });
    }

    $scope.deleteFromTradeList = function(item) {
      $http({
        url: 'https://'+URL_BASE+'/api/tradelist/delete/'+item.id,
        method: 'GET',
      }).then(function(data) {
        $scope.tradelist.splice( $scope.tradelist.indexOf(item), 1);
      }).then(function(error) {
        console.log(error);
      });
    }

    $scope.loadMorePosts = function() {
      if($rootScope.currentUser.hasOwnProperty('id')) {
        if($scope.isBusy === true) return;
        $scope.isBusy = true;

        if(!noMorePosts) {
          $http({
            url: 'https://'+URL_BASE+'api/posts?page='+$scope.currentPage+'&user-id='+$scope.currentUser.id,
            method: 'GET'
          }).then(function(data) {
              Array.prototype.push.apply($scope.posts, data.data.posts);
              console.log($scope.posts);
              if(data.data.posts.length < 4) {
                noMorePosts = true;
              }
              //Fixed my life @ 2:10am, Jan 17, 2016
              $scope.isBusy = false;
            }, function(error) {
              $scope.response = error.data;
          });

          $scope.currentPage++;

        }
      }
    }

    $rootScope.addRoute = function(url) {
      return 'https://www.blackmarketu.com/'+url;
    }


    $scope.updateUser = function() {
      $http({
        url: "https://blackmarketu.com/api/users/edit/user-information",
        method: "POST",
        data: {
          "bio":      $scope.currentUser.bio,
          "username": $scope.currentUser.username,
        }
      }).then(function(data) {
        alert("Account updated!");
        UserService.getUser();
      }).then(function(error) {
        console.log(error);
      })
    }

    $scope.updatePasswords = function() {
      $http({
        url: "https://www.blackmarketu.com/api/users/edit/user-password",
        method: "POST",
        data: {
          "password":            $scope.updatePassword.oldpassword,
          "n-password":          $scope.updatePassword.newpassword,
          "n-password-check":    $scope.updatePassword.passwordagain
        }
      }).then(function(data) {
        UserService.changePassword(data);
      }).then(function(error){
        console.log(error);
      });
    }

}]);

angular.module('bmuApp').controller('changeProPic', ["$scope", "$uibModal", "$log", "Upload", function($scope, $uibModal, $log, Upload) {

  $scope.animationsEnabled = true;

   $scope.open = function (_currentUser) {

     var modalInstance = $uibModal.open({
       animation: $scope.animationsEnabled,
       templateUrl: 'changeProPic.html',
       controller: 'ProPicModalInstanceCtrl',
       resolve: {
         user: function() {
           return _currentUser;
         }
       }
     });

     modalInstance.result.then(function () {

     }, function () {
       $log.info('Modal dismissed at: ' + new Date());
     });
   };

   $scope.toggleAnimation = function () {
     $scope.animationsEnabled = !$scope.animationsEnabled;
   };

 }]);

 angular.module('bmuApp').controller('ProPicModalInstanceCtrl', ["$scope", "$http", "$state", "Upload", "$rootScope", "$uibModalInstance", "user", function ($scope, $http, $state, Upload, $rootScope, $uibModalInstance, user) {

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

   $scope.user = user;

   $scope.posting = true;

   Upload.upload({
     url: 'https://blackmarketu.com/api/users/edit/profile-picture',
     data: {
       "profile-image":  dataUrl,
     }
   }).then(function (response) {
      $scope.posting = false;
      $scope.close();
    }, function (response) {
      if (response.status > 0)
      $scope.errorMsg = response.status + ': ' + JSON.stringify(response.data);
      $scope.posting = false;
      console.log($scope.errorMsg.error);
    }, function (evt) {
      //file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
    });
  };

   $scope.close = function() {
     $uibModalInstance.dismiss('cancel');
   };

   $scope.cancel = function () {
     $uibModalInstance.dismiss('cancel');
   };
 }]);


angular.module( 'bmuApp.profile')
.controller( "otherprofilecontroller", ["$scope", "$http", "$state", "UserService", "Upload", "$rootScope", "$stateParams", "URL_BASE", function OtherProfileController( $scope, $http, $state, UserService, Upload, $rootScope, $stateParams, URL_BASE) {

  $scope.hasPosts = false;
  var noMorePosts = false;
  $scope.hasPosts = false;
  $scope.currentPage = 0;
  $scope.posts = [];
  $scope.isBusy = false;
  $scope.updatePassword = {};
  $scope.newItem;
  $scope.tradelist = [];
  $scope.otherUser = {};


  /*****IMAGES LOADED******/
  $scope.imgLoadedEvents = {

      always: function(instance) {

      },

      done: function(instance) {
          angular.element(instance.elements[0]).addClass('loaded-ed animated fadeIn');
          $rootScope.$broadcast('masonry.reload');
      },

      fail: function(instance) {
          // Do stuff
      }

  };

  $rootScope.addRoute = function(url) {
    return 'https://www.blackmarketu.com/'+url;
  }

  $scope.privateMessenger = function(userId) {
    if(userId != UserService.currentUser().id) {
      $state.go("newMessageState",{id: userId});
    }
    else {
      console.log("your post idiot");
    }
  }

  $scope.loadMorePosts = function() {
    if($scope.isBusy === true) return;
    $scope.isBusy = true;

    if(!noMorePosts) {
      $http({
        url: 'https://'+URL_BASE+'api/posts?page='+$scope.currentPage+'&user-id='+$stateParams.id,
        method: 'GET'
      }).then(function(data) {
          Array.prototype.push.apply($scope.posts, data.data.posts);
          console.log($scope.posts);
          if(data.data.posts.length < 4) {
            noMorePosts = true;
          }
          //Fixed my life @ 2:10am, Jan 17, 2016
          $scope.isBusy = false;
        }, function(error) {
          $scope.response = error.data;
      });

      $scope.currentPage++;

    }
  }

  $scope.getTradeList = function() {
    $http({
      url: 'https://'+URL_BASE+'api/tradelist',
      method: "GET"
    }).then(function(data) {
      $scope.tradelist = data.data.tradelists;
    }).then(function(error) {
      console.log(error);
    });
  }

  $scope.getTradeList();

  $http({
    url: 'https://www.blackmarketu.com/api/users/'+$stateParams.id,
    method: "GET"
  }).then(function(data) {
    $scope.currentUser = data.data;
  }).then(function(error) {
    console.log(error);
  });

  console.log($scope.currentUser);


}]);
