<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/*View Composers*/

/*View::composer('authenticated.homepage', function($view) {
  	$view
  		->with('posts', Post::orderby('created_at','DESC')->take(10)->get())
  		->with('user', Auth::user())
  		->with('categories',Category::all());
});

View::composer('authenticated.mainsubmenu', function($view) {
  	$view
  		->with('user', Auth::user());
});
*/
Route::get('/', [
    'uses'=>'HomeController@home',
    'as'=>'home'
  ]);

/*All other Routes*/



//API endpoints
//API version if Version 1
//This segment should ONLY be used for the API

Route::api(['version' => 'v1', 'protected' => false], function() {

  //forgot to be taken out of the api prefix

	//API Prefix
	Route::group(['prefix' => 'api', 'protected' => false], function() {

		//Login route, unprotected
		Route::post('/login', 'AuthenticateController@authenticate');

    Route::post('/create-account', 'UserApiController@create');

    Route::post('/resend', 'UserApiController@resend');

    Route::post('/forgot/password','UserApiController@forgot');

    Route::post('/forgot/password/recover', 'UserApiController@recoverPassword');

    Route::get('forgot/password/{code}', 'UserApiController@forgotPassword');


    Route::group(['prefix' => 'verify','protected'=> false], function () {
      Route::post('/email','VerifyController@email');
      Route::post('/username','VerifyController@username');
    });

    Route::group(['prefix' => 'tradelist','protected'=> true], function () {
      Route::get('/','TradelistApiController@index');
      Route::get('/{id}','TradelistApiController@show');
      Route::get('/delete/{id}','TradelistApiController@delete');

      Route::post('/','TradelistApiController@create');
    });

    Route::group(['prefix' => 'chat','protected'=> true], function () {
      Route::get('/','ChatApiController@index');
      Route::post('/','ChatApiController@store');
      Route::get('/{id}','ChatApiController@show');
      Route::post('/auth','ChatApiController@auth');
    });

    Route::group(['prefix' => 'private-chat','protected'=> true], function () {
      Route::get('/','PrivateChatApiController@index');
      Route::post('/','PrivateChatApiController@store');
      Route::get('/{id}','PrivateChatApiController@show');
      Route::post('/auth','PrivateChatApiController@auth');
    });

		Route::group(['prefix' => 'users','protected'=> true], function () {
        	// These routes will be prefixed with 'users'
    		Route::get('/', 'UserApiController@index');
      	Route::get('/{id}','UserApiController@show');
        Route::get('/getlikes/{id}', 'UserApiController@getLikes');
        Route::post('/edit/profile-picture', 'UserApiController@updateProfilePicture');
        Route::post('/edit/user-information', 'UserApiController@edit');
        Route::post('/edit/user-password', 'UserApiController@resetPassword');
    });

    Route::group(['prefix' => 'posts','protected'=> true], function () {
      // These routes will be prefixed with 'posts'
      		Route::get('/', 'PostsApiController@index');
        	//Route::get('/{id}','PostsApiController@show');
          Route::get('/search', 'PostsApiController@search');
          Route::get('/delete/{id}','PostsApiController@delete');

          /*Post*/
          Route::post('/create', 'PostsApiController@store');
          Route::post('/like', 'PostsApiController@liked');
          Route::post('/sold', 'PostsApiController@sold');
          Route::post('/edit', 'PostsApiController@edit');
          Route::post('/report', 'PostsApiController@report');
    });

	});
});

/***End API***/

//Catch All Route!!!
App::missing(function($exception)
{
    return File::get(public_path() . '/index.html');
});


Route::get('/account/activate/{code}', array(
	'as' => 'account-activate',
	'uses' => 'AccountController@getActivate'
));

?>
