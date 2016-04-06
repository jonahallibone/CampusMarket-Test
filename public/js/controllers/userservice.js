angular.module('bmuApp').factory( 'UserService',
  function($http, URL_BASE, $http, store, $state, URL_BASE) {
    var currentUser = {};
    var loggedIn = false;


    return {

      createUser: function() {
        $state.go('loginState');
      },

      getLikes: function(id) {
        return $http({
          url: 'https://'+URL_BASE+'/api/users/getlikes/'+id,
          method: 'GET',
        }).then(
          function success(data, status, headers, config) {
            return data;
          }, function error(response) {
            console.log("Error");
            return false;
          }
        );
      },
      login: function(user) {
        var self = this;
        return $http({
          url: 'https://'+URL_BASE+'/api/login',
          method: 'POST',
          data: user
        }).then(
          function success(data, status, headers, config) {
            console.log(data);
            store.set('jwt', data.data.data.token);
            loggedIn = true;
            self.getUser();
            $state.go('postState');
            return true;
          }, function error(response) {
            console.log("Error");
            return false;
          }
        );
      },

      resend: function(email) {
        return $http( {
          url: 'https://'+URL_BASE+'api/resend',
          method: 'POST',
          data: {
            email: email
          }
        }).then(
          function success(response) {
            $state.go("loginState", { message: "Please check your email to activate your account!" });
            return true;
          }, function error(httpError) {
            return false;
          }
        );
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
});
