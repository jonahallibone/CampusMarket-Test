angular.module('bmuApp').factory( 'UserService',
  ["$http", "URL_BASE", "$http", "store", "$state", "URL_BASE", function($http, URL_BASE, $http, store, $state, URL_BASE) {
    var currentUser = {};
    var loggedIn = false;


    return {

      createUser: function() {
        $state.go('loginState');
      },

      login: function(user) {
        var self = this;
        $http({
          url: 'https://'+URL_BASE+'/api/login',
          method: 'POST',
          data: user
        }).then(function(response) {
          loggedIn = true;
          store.set('jwt', response.data.data.token);
          self.getUser();
          $state.go('postState');
          return true;
        }, function(error) {
          return false;
        });
      },

      getUser: function() {
        var self = this;
        if(store.get('jwt') !== null) {
        $http({
          url: 'https://'+URL_BASE+'api/users',
          method: 'GET'
        }).then(function(data) {
            currentUser = data.data;
            loggedIn = true;
          }, function(error) {
            self.logout();
          });
        }
      },
      logout: function(){
        var self = this;
        loggedIn = false;
        store.remove("jwt");
        $state.go('homeState');
      },
      changePassword: function(data) {
        var self = this;
        store.remove("jwt");
        store.set('jwt', data.data);
      },
      setLoggedIn: function() { loggedIn = true },
      isLoggedIn: function() { return loggedIn; },
      currentUser: function() { return currentUser; }
    };
}]);
