<?php

class AccountController extends BaseController {

	public function getCreate() {
		//This is for the front page stuff
		if(Input::has('edu-email')) {
			$the_email = Input::get('edu-email');

			return View::make('account.signup')
			->with('title', 'BlackMarket U | Sign Up')
			->with('fpemail',$the_email);
		}
		else {
			return View::make('account.signup')
			->with('title', 'BlackMarket U | Sign Up');
		}
	}

	public function postCreate() {
		$validator = Validator::make(Input::all(),
			array(
				'email' => array('required','max:50','email','unique:users,email','regex:/(\.edu(\.[a-zA-Z]+)?|\.ac\.[a-zA-Z]+)$/i'),
				'first-name' => 'required|max:20|min:3|',
				'last-name' => 'required|max:30|min:3|',
				'username' => 'required|max:30|min:3|unique:users,username|',
				'city' => '',
				'state' => '',
				'password-init' => array('required','min:6','AlphaNum','regex:/^[\pL\pN]*(?=[\pL\pN]*\pL)(?=[\pL\pN]*\pN)[\pL\pN]*$/'),
				'password-check' => 'required|min:6|max:50|AlphaNum|same:password-init'
			)
		);

		if($validator->fails()) {
			return Redirect::route('account-create')
				-> withErrors($validator)
				-> withInput();
		}
		else {
			$email = Input::get('email');
			$firstName = Input::get('first-name');
			$lastName = Input::get('last-name');
			$username = Input::get('username');
			$city = Input::get('city');
			$state = Input::get('state');
			$password = Input::get('password-init');

			$code = str_random(60);

			$user = User::create(array(
				"email" => Input::get('email'),
				"name" => Input::get('first-name'),
				"lastname" => Input::get('last-name'),
				"username" => Input::get('username'),
				"city" => Input::get('city'),
				"state" => Input::get('state'),
				"password" => Hash::make(Input::get('password-init')),
				"code" => $code,
				"active" => 0
			));

			$user->save();

			if($user) {

				//Send Email
				Mail::send('emails.auth.activate',array('username'=>$username, 'link' => URL::route('account-activate',$code)), function($message) use ($user) {
					$message -> to($user->email,$user->username) -> subject('Activate your account');
				});

				return Redirect::route('home')
					->with('global','Your account has been created. Please check email.');
			}

		}
	}

	public function getActivate($code) {
		$user = User::where('code', '=', $code)
					-> where('active', '=', 0);

		if($user->count()) {
			$user = $user->first();

			//Update user to active state

			$user->active = 1;
			$user->code = '';

			if($user->save()) {
				return Redirect::route('home')
					->with('global','Your account has been actived, please sign in.');
			}

		}

		return Redirect::route('home')
					->with('global','Your account could not be activated, please try again later.');
	}


	public function getLogin() {

		return View::make('account.login')->with('title', 'Campus Market | Login');
	}

	public function postLogin() {

		$validator = Validator::make(Input::all(),
			array(
				'email' => 'required|email',
				'password' => 'required'
			)
		);

		if($validator->fails()) {
			return Redirect::route('account-login')
								->withErrors($validator)
								->withInput();
		}

		else {

			$remember = (Input::has('remember-me')) ? true : false;
			$email = Input::get('email');
			$password = Input::get('password');

			if(Auth::attempt(array('email' => $email,'password' => $password,'active' => 1),$remember)) {
				Redirect::intended('/');
			}

			else {
				return Redirect::route('account-login')
							->with('global',"Email/password wrong");
			}

		}

		return Redirect::route('account-login')
							->with('global',"There was a problem signing you in.");
	}

	public function getLogout() {
		Auth::logout();
		return Redirect::route('home');
	}

	public function addToWaitingList() {
		$email = Input::get('email');
		$college = Input::get('college');
		$returner = "";

		$lister = Lister::create(array(
			'email' => $email,
			'college' => $college
		));

		$lister->save();

		if($lister) {
			$returner = "Thanks! We'll be sure to get in touch!";
		}
		else {
			$returner = "Oops - looks like there was an error!";
		}

		return $returner;
	}
}
?>
