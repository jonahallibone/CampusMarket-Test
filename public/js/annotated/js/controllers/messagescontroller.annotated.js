angular.module( 'bmuApp.pmessenger', [
  'ui.router',
  'angular-storage',
  'angular-jwt',
  'pusher-angular'
]).controller( 'messagescontroller', ["$scope", "$http", "store", "$state", "$stateParams", "UserService", "$pusher", "$rootScope", function MessagesController( $scope, $http, store, $state, $stateParams, UserService, $pusher, $rootScope) {

  $rootScope.addRoute = function(url) {
    return 'https://www.blackmarketu.com/'+url;
  }

  $scope.threads;
  $scope.message = {};
  $scope.currentUser = $rootScope.currentUser;


  $rootScope.getThreads = function() {
    $http({
      url: "https://www.blackmarketu.com/api/private-chat",
      method: "GET"
    }).then(function(data){
      if(Array.isArray(data.data)) {
        $scope.threads = data.data;
      }
      else $scope.threads = "No threads for this user";
    }).then(function(error){
      $scope.error = error;
    });
  };

  $rootScope.getThreads();

}]);


angular.module('bmuApp.pmessenger').controller('messageslistcontroller', ["$scope", "$http", "store", "$state", "UserService", "$pusher", "$stateParams", "$rootScope", function($scope, $http, store, $state, UserService, $pusher, $stateParams, $rootScope) {

    //Write a UserService function for this
    $http({
      url: "https://www.blackmarketu.com/api/users/"+$stateParams.id,
      method: "GET"
    }).then(function(data) {
      $scope.toUser = data.data;
    }).then(function(data) {
      $scope.notFound
    })

    $scope.initPusher = function(id)
    {
      var client = new Pusher('f30782470c4ffc2c0651', {
        authEndpoint: "https://www.blackmarketu.com/api/private-chat/auth",
        auth: {
          headers: {
            'Authorization': "Bearer " + localStorage.getItem('jwt'),
          }
        }
      });
      var pusher = $pusher(client);
      var channel = pusher.subscribe('private-' + id.toString());
      pusher.bind('new-private-message', function(data) {
        $scope.messages.push(data);
        $rootScope.getThreads();
      });
    }

    $scope.initPusher($stateParams.id)

    $scope.newMessage = {};

    $http({
      url: "https://www.blackmarketu.com/api/private-chat/"+$stateParams.id,
      method: "GET",
    }).then(function(data) {
      //console.log(data);
      $scope.messages = data.data.messages.reverse();
      $scope.otherUser = data.data.otherUser;
    }).then(function(error) {
      //console.log(error);
    })


  $scope.sendMessage = function() {
    message = $scope.newMessage.message;
    $scope.newMessage.message = "";
    //console.log("sending..")
    $http({
      url: "https://www.blackmarketu.com/api/private-chat",
      method: "POST",
      data: {
        "thread-id": $stateParams.id,
        "message":   message
      }
    }).then(function(data){
      $rootScope.getThreads();
    }).then(function(data){
      //console.log(data);
    })
  };

}]);



angular.module('bmuApp.pmessenger').controller('newmessagecontroller', ["$scope", "$http", "store", "$state", "UserService", "$pusher", "$stateParams", function($scope, $http, store, $state, UserService, $pusher, $stateParams) {

  $scope.newMessage = {};
  $scope.toUser = {}

  $http({
    url: "https://www.blackmarketu.com/api/users/"+$stateParams.id,
    method: "GET"
  }).then(function(data) {
    //console.log(data.data);
    $scope.toUser = data.data;
  }).then(function(data) {

  });

  $scope.sendMessage = function() {
    message = $scope.newMessage.message;
    $scope.newMessage.message = "";
    //console.log("sending..")
    $http({
      url: "https://www.blackmarketu.com/api/private-chat",
      method: "POST",
      data: {
        "to-user":   $stateParams.id,
        "message":   message
      }
    }).then(function(data){
      var route;
      console.log(data);
      if(data.data.hasOwnProperty("private_message")) {
        $state.go("messages",{ id:data.data.private_message.thread_id })
        $rootScope.getThreads();
      }
      else if(data.data.hasOwnProperty("message")) {
        $state.go("messages",{ id:data.data.message.thread_id })
      }

    }).then(function(data){
      console.log(data);
    })
  };
}]);


//Scroll Bottom Directive

angular.module('bmuApp').directive('scrollStick', function () {
  return {
    scope: {
      scrollStick: "="
    },
    link: function (scope, element) {
      scope.$watchCollection('scrollStick', function (newValue) {
        if (newValue)
        {
          $(element).scrollTop($(element)[0].scrollHeight);
        }
      });
    }
  }
});

angular.module('bmuApp').directive('enterSubmit', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.enterSubmit);
                });

                event.preventDefault();
            }
        });
    };
});
