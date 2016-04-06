angular.module( 'bmuApp.posts', [
  'ui.router',
  'angular-storage',
  'angular-jwt',
  'ngFileUpload',
  'angular-images-loaded',
  '720kb.tooltips'
]).controller( 'postscontroller', function PostsController($scope, $http, $state, UserService, Upload, $rootScope, URL_BASE) {

    $scope.posts = [];


    $rootScope.addPostToFrontPage = function(post) {
      $scope.posts.unshift(post.data);
    }

    $scope.currentPage = 1;

    var noMorePosts = false;
    $scope.isBusy = false;

    //Wait for images to load...
    //Called from DOM directive
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

  $rootScope.activeMenu = undefined;
  $rootScope.openMenu = undefined;

  //Header Collapse

  $rootScope.setOpen = function(id) {
    console.log(id);

    if($rootScope.openMenu === id) {
      $rootScope.openMenu = undefined;
    }
    else $rootScope.openMenu = id;

  }


  $rootScope.addRoute = function(path) {
    return 'https://www.blackmarketu.com'+path;
  }

  $scope.loadMorePosts = function() {

    if($scope.isBusy === true) return;
    $scope.isBusy = true;

    if(!noMorePosts) {

      $http({
        url: 'https://'+URL_BASE+'api/posts?page='+$scope.currentPage,
        method: 'GET'
      }).then(function(data) {
          Array.prototype.push.apply($scope.posts, data.data.posts);
          if(data.data.posts.length < 4) {
            noMorePosts = true;
            console.log(data.data);
          }
          //Fixed my life @ 2:10am, Jan 17, 2016
          $scope.isBusy = false;
        }, function(error) {
          $scope.response = error.data;
      });

      $scope.currentPage++;

    }
  }


});

angular.module( 'bmuApp.posts').controller( 'categorycontroller', function CategoryController( $scope, $http, store, $stateParams, $state, URL_BASE, UserService, $rootScope) {

  $scope.$on('LastRepeaterElement', function(){
    $rootScope.$broadcast('masonry.reload');
  });
  $scope.imgLoadedEvents = {

      always: function(instance) {
          // Do nothing
      },

      done: function(instance) {
          angular.element(instance.elements[0]).addClass('loaded-ed animated fadeIn');
          $rootScope.$broadcast('masonry.reload');
      },

      fail: function(instance) {
          // Error Instance
      }

  };

  //Setting Default and click categories

  //Default category
  $rootScope.setActive = function(id) {
    console.log(id);
    if($stateParams.id != undefined && Object.keys($stateParams.id).length < 1) {
      $rootScope.activeMenu = id;
   }
   else {
     if(id >= 100) {
       $rootScope.activeMenu = id;
     }
   }
  }

  if(parseFloat($stateParams.id[0]) < 100) {
    $rootScope.setActive(parseFloat($stateParams.id[0]));
  }
  else return;

  $scope.posts = [];

  $rootScope.addRoute = function(path) {
    return 'https://www.blackmarketu.com'+path;
  }

  var slides = $scope.slides = [];
  var currIndex = 0;

  $scope.currentPage = 1;

  var noMorePosts = false;
  $scope.isBusy = false;



  $scope.loadMorePosts = function() {

    if($scope.isBusy === true) return;
    $scope.isBusy = true;

    var queryString = "";

    //Build query string
    for(x=0;x<Object.keys($stateParams.id).length;x++) {
      queryString+=("&category[]="+$stateParams.id[x]);
    }

    if(!noMorePosts) {
      $http({
        url: "https://"+URL_BASE+"api/posts?"+queryString+"&page="+$scope.currentPage,
        method: 'GET'
      }).then(function(data) {
          Array.prototype.push.apply($scope.posts, data.data.posts);
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

    //console.log($scope.currentPage);
    //Fucks it up, better where it is now
    //$scope.isBusy = false;
  }

}).controller( 'searchcontroller', function SearchController( $scope, $http, store, $stateParams, $state, URL_BASE, UserService, $rootScope) {

  //On last load reload masonry
  $scope.$on('LastRepeaterElement', function(){
    $rootScope.$broadcast('masonry.reload');
  });

  $scope.imgLoadedEvents = {

      always: function(instance) {
          // Do stuff
      },

      done: function(instance) {
          angular.element(instance.elements[0]).addClass('loaded-ed animated fadeIn');
          $rootScope.$broadcast('masonry.reload');
      },

      fail: function(instance) {
          // Do stuff
      }

  };

  $scope.posts = [];

  $rootScope.addRoute = function(path) {
    return 'https://www.blackmarketu.com'+path;
  }

  $scope.currentPage = 1;

  var noMorePosts = false;
  $scope.isBusy = false;

  //Getting Posts

  $scope.loadMorePosts = function() {

    if($scope.isBusy === true) return;
    $scope.isBusy = true;

    if(!noMorePosts) {
      $http({
        url: "https://blackmarketu.com/api/posts/search?query="+$stateParams.query+"&page="+$scope.currentPage,
        method: 'GET'
      }).then(function(data) {
          Array.prototype.push.apply($scope.posts, data.data.posts);
          console.log(data);
          if(data.data.length < 4) {
            noMorePosts = true;
          }
          //Fixed my life @ 2:10am, Jan 17, 2016
          $scope.isBusy = false;
        }, function(error) {
          console.log(error.data.error);
      });

      $scope.currentPage++;

    }
  }

});

angular.module('bmuApp').directive('likePost', ["$http", "$timeout", function($http, $timeout) {
  return {
    scope: {
      post: '=thePost', //scope for the individual post
      posts: '=thePosts' //scope for the posts array, for splicing
    },
    restrict: 'EA',
    replace: true, //replace true, makes element clickable, technically depracted
    template: "<i class='fa fa-heart-o icons right heart'></i>",
    link: function(scope, element, attrs) {

      //apply heart to post on load if it is liked by the user
      if(scope.post.likes.isLiked) {
        //remove defauly heart outline class
        element.removeClass("fa-heart-o").addClass("fa-heart liked animated bounceIn");
      }
      element.bind('click', function(e) {
        //post to like URL to get information
        $http({
          url: "https://blackmarketu.com/api/posts/like",
          method: 'POST',
          data: {
            "post-id": scope.post.id
          }
        }).then(function(data){
          //is element liked?
          if(scope.post.likes.isLiked) {
            //set it to not liked
            scope.post.likes.isLiked = false;
          }
          //is element not liked?
          else if(!scope.post.likes.isLiked) {
            //set element to liked
            scope.post.likes.isLiked = true;
          }

          //if element is not liked
          if(element.hasClass("fa-heart-o")) {
            //remove outline class
            element.removeClass("fa-heart-o animated bounceIn");
            //timeout to reapply animation
            $timeout(function(){
              //add animation class, and filled in heart
              element.addClass("fa-heart liked animated bounceIn");
            }, 1);
            //add one to like count
            scope.post.likes.likes++;
          }
          //if element is liked
          else if(element.hasClass("fa-heart liked")) {
            //remove filled in class
            element.removeClass("fa-heart liked animated bounceIn");
            //timeout to reapply animation
            $timeout(function(){
              //add animation class and outline heart
              element.addClass("fa-heart-o animated bounceIn");
            }, 1);
            scope.post.likes.likes--;
          }
        }).then(function(error) {
          //console.log(error);
        });
      });
    }
  }
}]);

angular.module('bmuApp').directive('messageUser',['$compile', 'UserService', '$state', function($compile, UserService, $state) {
  return {
    scope: {
      post: '=thePost',
      posts: '=thePosts'
    },
    restrict: 'EA',
    replace: true,
    template: "<i class='fa fa-comment-o icons right message'></i>",
    link: function(scope, element, attrs) {
      element.bind('click', function(e){
        if(scope.post.user_id != UserService.currentUser().id) {
          element.removeClass("fa-comment-o").addClass("fa-comment messaged");
          $state.go("newMessageState",{id: scope.post.user_id});
        }
        else {
          console.log("Your post");
        }
      });
    }
  }
}]);

angular.module('bmuApp').directive('deletePost', ["$http","$rootScope", function($http, $rootScope){
  return {
    scope: {
      post: '=thePost',
      posts: '=thePosts',
    },
    restrict: 'EA',
    link: function(scope, element, attr) {
      element.bind("click", function() {
        scope.post.deleting = true;
        $http({
          url: 'https://blackmarketu.com/api/posts/delete/'+scope.post.id,
          method: "GET",
        }).then(function(data) {
          scope.post.deleting = false;
          scope.posts.splice( scope.posts.indexOf(scope.post), 1);
          $rootScope.$broadcast('masonry.reload');
        }).then(function(error) {
          console.log(error);
        });
      });
    }
  }
}]);

angular.module('bmuApp').directive('editPost', ["$http","$rootScope", function($http, $rootScope){
  return {
    scope: {
      post: '=thePost',
      posts: '=thePosts',
    },
    restrict: 'EA',
    link: function(scope, element, attr) {
      element.bind("click", function() {
        scope.post.deleting = true;
        $http({
          url: 'https://blackmarketu.com/api/posts/delete/'+scope.post.id,
          method: "GET",
        }).then(function(data) {
          scope.post.deleting = false;
          scope.posts.splice( scope.posts.indexOf(scope.post), 1);
          $rootScope.$broadcast('masonry.reload');
        }).then(function(error) {
          console.log(error);
        });
      });
    }
  }
}]);

angular.module('bmuApp').directive('markAsSold', ["$http","$rootScope", function($http, $rootScope){
  return {
    scope: {
      post: '=thePost',
      posts: '=thePosts',
    },
    restrict: 'EA',
    link: function(scope, element, attr) {
      element.bind("click", function() {
        scope.post.deleting = true;
        $http({
          url: 'https://blackmarketu.com/api/posts/sold/',
          method: "POST",
          data: {
            "post-id": scope.post.id
          }
        }).then(function(data) {
          scope.post.deleting = false;
          scope.post.sold = true;
        }).then(function(error) {
          console.log(error);
        });
      });
    }
  }
}]);
