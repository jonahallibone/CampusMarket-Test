//App declaration
angular.module('bmuApp', [
  'wu.masonry',
  'ngAnimate',
  'ngSanitize',
  'ui.bootstrap',
  'flow',
  'angular-jwt',
  'angular-storage',
  'ui.router',
  'ngFileUpload',
  'ngTouch',
  'infinite-scroll',
  'angular-images-loaded',
  'pusher-angular',
  'ngImgCrop',
  '720kb.tooltips',
  'uiGmapgoogle-maps',
  'bmuApp.routes',
  'bmuApp.login',
  'bmuApp.signup',
  'bmuApp.posts',
  'bmuApp.pmessenger',
  'bmuApp.profile'
]).config( function myAppConfig ($urlRouterProvider, $urlMatcherFactoryProvider, jwtInterceptorProvider, $httpProvider,$locationProvider) {

  //Setting up routes and AUTH
  $locationProvider.html5Mode(true);
  $urlRouterProvider.otherwise('/');
  $urlMatcherFactoryProvider.strictMode(false);

  jwtInterceptorProvider.tokenGetter = function(store) {
    return store.get('jwt');
  }

  $httpProvider.interceptors.push('jwtInterceptor');
})
.run(function($rootScope, $state, $stateParams, store, jwtHelper, UserService) {
  $rootScope.$on('$stateChangeStart', function(e, to, toParams) {
    if($rootScope.isLoggedIn) {
      if(to.name === "signupState" || to.name === "loginState") {
        e.preventDefault();
        $state.go('homeState');
      }
      if(to.name === "otherProfileState") {
        if(toParams.id == $rootScope.currentUser.id) {
          e.preventDefault();
          $state.go("profileState");
        }
      }
    }


    if (to.data && to.data.requiresLogin) {
      if (!store.get('jwt') || jwtHelper.isTokenExpired(store.get('jwt'))) {
        e.preventDefault();
        $state.go('homeState');
      }
    }
  });
})


//Constants

angular.module('bmuApp').constant('URL_BASE', "blackmarketu.com/");

//Flow Config
angular.module('bmuApp').config([
  'flowFactoryProvider', function (flowFactoryProvider) {
  flowFactoryProvider.defaults = {
    target: '',
    permanentErrors: [500, 501],
    maxChunkRetries: 1,
    chunkRetryInterval: 5000,
    simultaneousUploads: 1
  };
  flowFactoryProvider.on('catchAll', function (event) {

  });
    // Can be used with different implementations of Flow.js
    // flowFactoryProvider.factory = fustyFlowFactory;
}]);

//Google Maps config

angular.module('bmuApp').config(
  function(uiGmapGoogleMapApiProvider) {
    uiGmapGoogleMapApiProvider.configure({
        //    key: 'your api key',
        v: '3.20', //defaults to latest 3.X anyhow
        libraries: 'weather,geometry,visualization'
    });
});


//Report Post Modal
angular.module('bmuApp').controller('reportPostModal', function($scope, $uibModal, $log) {

  $scope.animationsEnabled = true;

   $scope.openReport = function (size, _post, _posts) {

     var modalInstance = $uibModal.open({
       animation: $scope.animationsEnabled,
       templateUrl: 'reportPostModal.html',
       controller: 'ReportModalInstanceCtrl',
       size: size,
       resolve: {
         post: function() {
           return _post;
         },
         posts: function() {
           return _posts;
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

 });

//Add Post Modal
angular.module('bmuApp').controller('addPostModal', function($scope, $uibModal, $log) {

  $scope.animationsEnabled = true;

   $scope.open = function (size) {

     var modalInstance = $uibModal.open({
       animation: $scope.animationsEnabled,
       templateUrl: 'addPostModal.html',
       controller: 'ModalInstanceCtrl',
       size: size
     });

     modalInstance.result.then(function () {

     }, function () {
       $log.info('Modal dismissed at: ' + new Date());
     });
   };

   $scope.toggleAnimation = function () {
     $scope.animationsEnabled = !$scope.animationsEnabled;
   };

 });
/****EDIT POST MODAL**/
 angular.module('bmuApp').controller('editPostModal', function($scope, $uibModal, $log) {

   $scope.animationsEnabled = true;

    $scope.openEdit = function (_post) {
      var modalInstance = $uibModal.open({
        animation: $scope.animationsEnabled,
        templateUrl: 'editPostModal.html',
        controller: 'EditModalInstanceCtrl',
        resolve: {
          post: function() {
            return _post;
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

  });

 // Please note that $modalInstance represents a modal window (instance) dependency.
 // It is not the same as the $uibModal service used above.
 angular.module('bmuApp').controller('ReportModalInstanceCtrl', function ($scope, $http, $state, Upload, $rootScope, $uibModalInstance, post, posts) {

   $scope.categories = $rootScope.categories;
   $scope.item = {};
   $scope.posting = false;
   $scope.post= post;
   $scope.posts = posts;

   $scope.setFormScope= function(scope){
     this.formScope = scope;
   }

   $scope.uploadReport = function() {
     $scope.posting = true;
     if($scope.report.reason === "other") {
       $scope.report.reason = $scope.report.oreason;
     }
     $http({
       url: 'https://www.blackmarketu.com/api/posts/report',
       method: 'POST',
       data: {
         'post-id': $scope.post.id,
         'reason' : $scope.report.reason
       }
     }).then(function(data) {
       $scope.posting = false;
       $scope.posts.splice( $scope.posts.indexOf($scope.post), 1);
       $scope.close();

     }).then(function(error) {
       console.log(error);
     });
    };

   $scope.close = function() {
     $uibModalInstance.dismiss('cancel');
   };

   $scope.cancel = function () {
     $uibModalInstance.dismiss('cancel');
   };
 });

 angular.module('bmuApp').controller('EditModalInstanceCtrl', function ($scope, $http, $state, Upload, $rootScope, $uibModalInstance, post) {

   $scope.categories = $rootScope.categories;
   $scope.item = {};
   $scope.itemImages = [];
   $scope.posting = false;
   $scope.post = post;

   $scope.setFiles = function($files) {
     if($scope.itemImages.length < 3 && ($scope.itemImages.length+$files.length) <= 3) {
       Array.prototype.push.apply($scope.itemImages, $files);
     }
     console.log($scope.itemImages);
   }

   $scope.deleteImage = function($item) {
     $scope.itemImages.splice($item, 1);
   }

   $scope.setFormScope= function(scope){
     this.formScope = scope;
   }

   $scope.item.desc = $scope.post.content;
   $scope.item.name  = $scope.post.title;
   $scope.item.price = parseFloat($scope.post.price);

   $scope.uploadPost = function() {
     $scope.posting = true;
     $http({
       url: 'https://www.blackmarketu.com/api/posts/edit',
       method: 'POST',
       data: {
         'price': $scope.item.price,
         'title': $scope.item.name,
         'content': $scope.item.desc,
         'post-id': $scope.post.id
       }
     }).then(function(data) {

       $scope.post.content = $scope.item.desc;
       $scope.post.title = $scope.item.name;
       $scope.post.price = $scope.item.price;

       $scope.posting = false;
       $scope.close();

     }).then(function(error) {
       console.log(error);
     });
    };

   $scope.close = function() {
     $uibModalInstance.dismiss('cancel');
   };

   $scope.cancel = function () {
     $uibModalInstance.dismiss('cancel');
   };
 });

 angular.module('bmuApp').controller('ModalInstanceCtrl', function ($scope, $http, $state, Upload, $rootScope, $uibModalInstance) {

   $scope.categories = $rootScope.categories;
   $scope.item = {};
   $scope.itemImages = [];
   $scope.posting = false;
   $scope.activeCategory = null;

   $scope.setFiles = function($files) {
     if($scope.itemImages.length < 3 && ($scope.itemImages.length+$files.length) <= 3) {
       Array.prototype.push.apply($scope.itemImages, $files);
     }
     console.log($scope.itemImages);
   }

   $scope.deleteImage = function($item) {
     $scope.itemImages.splice($item, 1);
   }

   $scope.setFormScope = function(scope){
     this.formScope = scope;
   }

   $scope.uploadPost = function(files) {

     $scope.imageError = false;

     item = $scope.item;


      if($scope.itemImages && $scope.itemImages.length) {

        $scope.posting = true;
        Upload.base64DataUrl($scope.itemImages).then(function(urls){
          $scope.itemImages.upload = Upload.upload({
            url: 'https://blackmarketu.com/api/posts/create',
            data: {
              "item-image":         urls,
              "item-title":         item.name,
              "item-price":         item.price,
              "item-description":   item.desc,
              "category-id":        item.category
            }
          });

          $scope.itemImages.upload.then(function (response) {
            $scope.posting = false;
            $rootScope.addPostToFrontPage(response);
            $scope.close();
          }, function (response) {
              console.log(response);
              $scope.posting = false;
          });
      });
     }
     else {
       $scope.imageError = true;
       console.log("Invalid");
     }
    };

   $scope.close = function() {
     $uibModalInstance.dismiss('cancel');
   };

   $scope.cancel = function () {
     $uibModalInstance.dismiss('cancel');
   };
 });


angular.module('bmuApp').directive('searchAble', function($state, $stateParams) {
  return {
        restrict: 'A',
        link: function (scope, element, attrs) {
          scope.$watch($stateParams.query, function (param){
            if(param) {
              console.log();
              attrs.ngModel = param.query;
            }
          });
          scope.$watch(attrs.ngModel, function (v) {
              if(v) {
                $state.go("searchState", { "query": v });
              }
            });
        }
    };
});

angular.module('bmuApp').directive('messageNoti', function($timeout){
  return {
    scope: {
      alert: '=messageNoti',
      alerts: '=messageArray'
    },
    restrict: 'EA',
    templateUrl: '/partials/authenticated/homepage/alerts.html',
    link: function(scope, element, attrs) {
         $timeout(function(){
            element.addClass('fadeOut');
            $timeout(function(){
              //Remove element
              element.remove();
              //Remove from the alert array!!
              scope.alerts.splice(alert, 1);
            }, 500);
         }, 5000);

        element.on('$destroy', function () {
          //Destroy scope of the directive
          scope.$destroy();
        });
       }
    };
});

/*****************/
/***APP RUN*******/
/*****************/
angular.module('bmuApp').run(function($rootScope, UserService, $pusher) {
  $rootScope.alerts = [];

  $rootScope.currentUserId;

  var connected = false;

  $rootScope.$watch( UserService.currentUser, function (user) {
    $rootScope.currentUser = user;
    $rootScope.currentUserId = user.id;

    if($rootScope.currentUser.hasOwnProperty("id") && !connected) {
      connected = true;

      var client = new Pusher('f30782470c4ffc2c0651', {
        authEndpoint: "https://www.blackmarketu.com/api/private-chat/auth",
        auth: {
          headers: {
            'Authorization': "Bearer " + localStorage.getItem('jwt'),
          }
        }
      });
      var pusher = $pusher(client);
      var channel_name = "private-" + $rootScope.currentUser.id;
      var private_channel = pusher.subscribe(channel_name);
      private_channel.bind('private-message-alert', function(data) {
        $rootScope.alerts.push(data);
        console.log($rootScope.alerts);
      });
    }
  });

    //Define Categories

    $rootScope.categories = [
      {
        id:         null,
        name:       "Choose category",
        selectable: true
      },
      {
        id:         null,
        name:       "Buy & Sell",
        selectable: true
      },
      {
        id:         0,
        name:       "Miscellaneous",
        selectable: false
      },
      {
        id:         1,
        name:       "Clothing / Accessories",
        selectable: false
      },
      {
        id:         2,
        name:       "Furniture / Homegoods",
        selectable: false
      },
      {
        id:         3,
        name:       "Electronics",
        selectable: false
      },
      {
        id:         4,
        name:       "Books",
        selectable: false
      },
      {
        id:         5,
        name:       "Tickets",
        selectable: false
      },
      {
        id:         6,
        name:       "Sports & Leisure",
        selectable: false
      },
      {
        id:         null,
        name:       "Bulletin Board",
        selectable: true
      },
      {
        id:         7,
        name:       "Events",
        selectable: false
      },
      {
        id:         8,
        name:       "Questions",
        selectable: false
      },
      {
        id:         9,
        name:       "Clubs / Activities",
        selectable: false
      },
      {
        id:         10,
        name:       "Services",
        selectable: false
      },
      {
        id:         null,
        name:       "Rideshare",
        selectable: true
      },
      {
        id:         11,
        name:       "Rideshare",
        selectable: false
      },
      {
        id:         null,
        name:       "Jobs & Internships",
        selectable: true
      },
      {
        id:         12,
        name:       "Jobs",
        selectable: false
      },
      {
        id:         13,
        name:       "Internships",
        selectable: false
      },
      {
        id:         null,
        name:       "Housing",
        selectable: true
      },
      {
        id:         14,
        name:       "Housing Wanted",
        selectable: false
      },
      {
        id:         15,
        name:       "Housing Offering",
        selectable: false
      },
      {
        id:         null,
        name:       "Lost & Found",
        selectable: true
      },
      {
        id:         16,
        name:       "Lost & Found",
        selectable: false
      },

    ];
});

angular.module("bmuApp").filter('slice', function() {
  return function(arr, start, end) {
    return arr.slice(start, end);
  };
});
