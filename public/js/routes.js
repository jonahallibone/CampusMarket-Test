angular.module( 'bmuApp.routes', [
  'ui.router',
  'angular-storage'
])
.config(function($stateProvider, $urlRouterProvider) {
  //Root state for mainviews on the homepage. E.g. Posts and search
  $stateProvider.state('root', {
    abstract: true,
    url: '/',
    views: {
      '': {
        templateUrl: 'partials/authenticated/homepage/homepage.html',
        controller: 'postscontroller', //In postscontroller.js
      },
    controller: 'postscontroller'
    },
    data: {
      requiresLogin: true
    }
  }).state('postState', {
    parent: 'root',
    url: '',
    templateUrl: 'partials/authenticated/homepage/posts.html',
    controller: 'postscontroller', //In postscontroller.js
    data: {
      requiresLogin: true
    }
  }).state('categoryStateHome', {
    parent: 'root',
    url: '/category/',
    controller: function($state, $stateParams){
      $state.go("postState");
    },
    data: {
      requiresLogin: true
    }
  }).state('categoryState', {
    parent: 'root',
    url: 'category?:id',
    params: {
      id: { array: true }
    },
    templateUrl: 'partials/authenticated/homepage/posts.html',
    controller: 'categorycontroller', //In postscontroller.js
    data: {
      requiresLogin: true
    }
  }).state('searchState', {
    parent: 'root',
    url: 'search?query',
    templateUrl: 'partials/authenticated/homepage/posts.html',
    controller: 'searchcontroller', //In postscontroller.js
    data: {
      requiresLogin: true
    }
  }).state('logoutState', {
    url: '/logout',
    controller: 'logoutcontroller'
  }).state('loginState', {
    url: '/login',
    templateUrl: 'partials/unauthenticated/login.html',
    controller: 'logincontroller', //In logincontroller.js
    params: {
      message: null,
      alert: null
    }
  }).state('signupState',{
    url: '/signup',
    templateUrl: 'partials/unauthenticated/signup.html',
    controller: 'signupcontroller' //In signupcontroller.js
  }).state('profileState', {
    url: '/profile/',
    views: {
      '': {
        templateUrl: 'partials/authenticated/profiles/newprofile.html',
        controller: 'profilecontroller', //In profilecontroller.js
      },
      'posts@profileState': {
        templateUrl: 'partials/authenticated/homepage/posts.html',
        controller: 'profilecontroller', //In profilecontroller.js
      },
      data: {
        requiresLogin: true
      }
    }
  }).state('otherProfileState', {
    url: '/profile/:id',
    views: {
      '': {
        templateUrl: 'partials/authenticated/profiles/newprofile.html',
        controller: 'otherprofilecontroller', //In profilecontroller.js
      },
      'posts@otherProfileState': {
        templateUrl: 'partials/authenticated/homepage/posts.html',
        controller: 'otherprofilecontroller', //In profilecontroller.js
      },
      data: {
        requiresLogin: true
      }
    }
  }).state('privateMessagesState', {
    url: '/messages',
    cache: false,
    views: {
      '': {
        templateUrl: 'partials/authenticated/messenger/messages.html',
        controller: 'messagescontroller', //In messagescontroller.js
      },
      /*'posts@privateMessagesState': {
        templateUrl: 'partials/authenticated/messenger/liked-posts.html',
        controller: 'messageslistcontroller', //In messagescontroller.js
      }*/
    },
    controller: 'messagescontroller', //In messagescontroller.js
    data: {
      requiresLogin: true
    }
  }).state('messages', {
    parent: 'privateMessagesState',
    url: '/:id',
    templateUrl: 'partials/authenticated/messenger/messagelist.html',
    controller: 'messageslistcontroller', //In messagescontroller.js
    data: {
      requiresLogin: true
    }
  }).state('newMessageState', {
    parent: 'privateMessagesState',
    url: '/compose/:id',
    templateUrl: 'partials/authenticated/messenger/newmessage.html',
    controller: 'newmessagecontroller', //In messagescontroller.js
    data: {
      requiresLogin: true
    }
  }).state('termsState', {
    url: '/terms',
    templateUrl: 'partials/unauthenticated/termsofagreement.html',
    data: {
      requiresLogin: false
    }
  }).state('forgotState', {
    url: '/forgot',
    templateUrl: 'partials/unauthenticated/forgot.html',
    data: {
      requiresLogin: false
    },
    controller: 'forgotcontroller' //logincontroller.js
  }).state('forgotStateCode', {
    url: '/forgot/password/:code',
    templateUrl: 'partials/unauthenticated/reset-password.html',
    data: {
      requiresLogin: false
    },
    controller: 'recovercontroller' //logincontroller.js
  }).state('resendState', {
    url: '/resend',
    templateUrl: 'partials/unauthenticated/resend.html',
    data: {
      requiresLogin: false
    },
    controller: 'resendcontroller' //logincontroller.js
  }).state('homeState', {
    url: '/home',
    templateUrl: 'partials/unauthenticated/landing-page.html',
    controller: 'postscontroller', //In postscontroller.js
  })


});
