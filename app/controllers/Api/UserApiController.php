<?php
use Carbon\Carbon;

class UserApiController extends BaseController {

	public function index() {

		JWTAuth::parseToken();
		$toReturn = [];
		$user = JWTAuth::parseToken()->toUser();
		if($user) {
			$getLikes = DB::table("liked_posts")
														->join("posts", function($join) use ($user) {
																$join->on("liked_posts.post_id","=","posts.id")
																	 ->where("posts.posted_by","=",$user->id)
																	 ->where("liked_posts.is_liked","=",1);
														})
														->count();
			$toReturn = [
				'firstName' => $user->name,
				'lastname' => $user->lastname,
				'username' => $user->username,
				'email' => $user->email,
				'school' => School::find($user->school)->school_name,
				'bio' => $user->user_bio,
				'grad_class' => $user->grad_class,
				'profile_picture' => $user->profile_picture,
				'id' => $user->id,
				'likes'=> $getLikes,
				'for_sale' => Post::where("posted_by","=",$user->id)
														->where("soft_delete","=",0)
														->where("sold","=",0)
														->where("reported","=",0)
														->count(),
				'sold' => Post::where("posted_by", "=", $user->id)
												->where("soft_delete","=",0)
												->where("sold","=",1)
												->count()
			];
		}

		return $toReturn;
	}
//Add member function to user class for chaning profile picture.
	public function updateProfilePicture() {
		JWTAuth::parseToken();
		$user = JWTAuth::parseToken()->toUser();

		$oFile = Input::get("profile-image");

		if($oFile!="") {
			$img = Image::make($oFile)->orientate();

			//return $theReturn;
			//if larger than 1920, make smaller
			$img->widen(720, function ($constraint) {
				$constraint->upsize();
			});
				//if taller than 1080, make smaller than 1080
			$img->heighten(720, function ($constraint) {
				$constraint->upsize();
			});


			$fileName = Uuid::generate();
			$extenstion = '.png';
			$fileItself = $fileName . '.' . $extenstion;

			$path = "/profile-images/" . $fileItself;

			$img->save(public_path($path));

			$user->profile_picture = $path;
			$user->save();

			return $this->response->noContent();

		}

		else return $this->response->errorInternal();



	}

	public function show($id) {


		JWTAuth::parseToken();
		$realUser = JWTAuth::parseToken()->toUser();

		$toReturn = [];
		$user = User::find($id);
		if($user) {
			$getLikes = DB::table("liked_posts")
														->join("posts", function($join) use ($user) {
																$join->on("liked_posts.post_id","=","posts.id")
																	 ->where("posts.posted_by","=",$user->id)
																	 ->where("liked_posts.is_liked","=",1);
														})
														->count();
			$toReturn = [
				'firstName' => $user->name,
				'lastname' => $user->lastname,
				'username' => $user->username,
				'email' => $user->email,
				'school' => School::find($user->school)->school_name,
				'bio' => $user->user_bio,
				'grad_class' => $user->grad_class,
				'profile_picture' => $user->profile_picture,
				'id' => $user->id,
				'likes'=> $getLikes,
				'for_sale' => Post::where("posted_by","=",$user->id)
														->where("soft_delete","=",0)
														->where("sold","=",0)
														->where("reported","=",0)
														->count(),
				'sold' => Post::where("posted_by", "=", $user->id)
												->where("soft_delete","=",0)
												->where("sold","=",1)
												->count()
			];
			return $toReturn;
		}

		else return $this->response->error('User not found', 401);

}

public function create() {


	$code = str_random(60);


	$validator = Validator::make(Input::all(),
		array(
			'email' => array('required','max:50','email','unique:users,email','regex:/(\.edu(\.[a-zA-Z]+)?|\.ac\.[a-zA-Z]+)$/i'),
			'first-name' => 'required|max:20|min:3|',
			'last-name' => 'required|max:30|min:3|',
			'username' => 'required|max:30|min:3|unique:users,username',
			'password-init' => 'required|min:6|max:50|alpha_dash',
			'password-check' => 'required|min:6|max:50|alpha_dash|same:password-init',
			"grad-class" => 'required|min:2014|max:2024|numeric',
			"school" => 'exists:schools,school_name,required'
		)
	);

	if($validator->fails()) {
		return $this->response->error('Error making account', 401);
	}


	$user = User::create(array(
		"email" => Input::get('email'),
		"name" => Input::get('first-name'),
		"lastname" => Input::get('last-name'),
		"username" => Input::get('username'),
		"password" => Hash::make(Input::get('password-init')),
		"grad_class" => Input::get('grad-class'),
		"school" => Input::get('school-id'),
		"code" => $code,
		"active" => 0
	));

	if($user) {

		$oFile = Input::get("profile-image");

		if($oFile!="") {
			$img = Image::make($oFile);

			//return $theReturn;
			//if larger than 1920, make smaller
			$img->widen(1920, function ($constraint) {
				$constraint->upsize();
			});
				//if taller than 1080, make smaller than 1080
			$img->heighten(1080, function ($constraint) {
				$constraint->upsize();
			});


			$fileName = Uuid::generate();
			$extenstion = '.png';
			$fileItself = $fileName . '.' . $extenstion;

			$path = "/profile-images/" . $fileItself;

			$img->save(public_path($path));

			$user->profile_picture = $path;

			Mail::send('emails.auth.activate',array('username'=>$user->username, 'link' => URL::route('account-activate',$code)), function($message) use ($user) {
				$message -> to($user->email,$user->username) -> subject('Activate your account');
			});


			$user->save();


		}

		else return $this->response->error('Error making account', 401);
	}

	else return $this->response->error('Error making account', 401);
}

public function edit() {

	JWTAuth::parseToken();
	$user = JWTAuth::parseToken()->toUser();

	$user->user_bio = Input::get('bio');
	$user->username = Input::get('username');

	$toReturn = "Error";

	if($user->save()) {
		$toReturn = "Information updated";
	}

	else {
		$toReturn = "There was a problem. Please try again later";
	}

	return $toReturn;

}

public function forgotPassword($code) {

	$query = Reset::where('token','=',$code);
	if($query->exists()) {

		$reset = $query->first();

		if($reset->updated_at > Carbon::now()->subHours(2)) {
			return $this->response->noContent();
		}
		else return $this->response->errorInternal();
	}

	else return $this->response->errorInternal();
}

public function recoverPassword() {

	$validator = Validator::make(Input::all(),
		array(
			'password' => 'required|min:6|max:50|alpha_dash',
			'passwordCheck' => 'required|min:6|max:50|alpha_dash|same:password',
			'code' => 'required|exists:password_reminders,token'
			)
		);

		$code = Input::get('code');
		$password = Input::get('password');

		if($validator->fails()) {
			return $this->response->error('Error recovering account', 401);
		}

		$reset = Reset::where("token","=",$code)->first();
		$user = User::where("email","=",$reset->email)->first();

		$reset->token = '';
		$reset->save();

		$user->update([
			'password' => Hash::make($password)
		]);


		return $this->response->noContent();



}

public function resetPassword() {

	JWTAuth::parseToken();
	$token = JWTAuth::getToken();
	$user = JWTAuth::parseToken()->toUser();


	$inPassword = Input::get('password');
	$newPassword = Input::get('n-password');
	$newPasswordCheck = Input::get('n-password-check');

	if(Hash::check($inPassword, $user->password)) {
		if($newPassword == $newPasswordCheck) {
			$password = Hash::make($newPassword);
			$user->password = $password;
			$user->save();
			JWTAuth::setToken($token)->invalidate();
			$nToken = JWTAuth::fromUser($user);

			return $nToken;
		}

		else return $this->response->error('Passwords do not match', 401);

	}

	return $this->response->error("Incorrect user password", 401);

}

	public function forgot() {

		if(Input::get('email')) {

		//	$user = User::where('username','=',Input::get('username'));
			$email = Input::get('email');

			if(User::where('email','=',$email)->exists()) {

				$code = str_random(60);

				if(Reset::where('email','=',$email)->exists()) {
					$currentReset = Reset::where('email','=',$email)->first();
					$currentReset->update([
						'token' => $code
					]);
				}
				else {
					$newReset = Reset::create(array(
						'email' => $email,
						'token' => $code
					));
					$newReset->save();
				}

				$user = User::where('email','=',$email)->first();
				$link = "https://www.blackmarketu.com/forgot/password/" . $code;
				Mail::send('emails.auth.reminder', array('username'=>$user->username, 'link' => $link), function($message) use ($user) {
					$message
						->to($user->email,$user->username)
						->subject('Reset your password with BlackMarket U');
				});

			}
			else return $this->response->errorInternal();
		}

		else return $this->response->errorInternal();
	}

	public function resend() {

		if(User::where('email','=',Input::get('email'))->exists()) {
			$user = User::where('email','=',Input::get('email'))->select('username', 'email', 'code', 'active')->first();
			$code = $user->code;
			//If account is not active
			if($user->active != 1) {
				Mail::send('emails.auth.activate',array('username'=>$user->username, 'link' => URL::route('account-activate',$code)), function($message) use ($user) {
					$message -> to($user->email,$user->username) -> subject('Activate your account');
				});

				return $this->response->noContent();
			}
			else return $this->response->errorInternal();
		}

		else return $this->response->errorInternal();
	}

	public function getLikes($id) {
		$user = JWTAuth::parseToken()->toUser();
		$getPostId = [];
		$response = [];
		if(User::find($id)->exists()) {
			$getLikedPosts = DB::table("liked_posts")
														->join("posts", function($join) use ($id) {
																$join->on("liked_posts.post_id","=","posts.id")
																	 ->where("posts.posted_by","=",$id)
																	 ->where("posts.soft_delete","=",0)
																	 ->where("posts.sold","=",0)
																	 ->where("posts.reported","=",0)
																	 ->where("liked_posts.is_liked","=",1);
														})
														->select("posts.id")
														->take(5)
														->get();
				foreach($getLikedPosts as $post) {
					$getPostId[] = $post->id;
				}

				$posts = Post::whereIn("id",$getPostId)->get();

				$cUser = User::find($id);
				foreach ($posts as $post) {
					$response['posts'][] = [
              'id' => $post->id,
							'user_id' => $cUser->id,
              'username' => $cUser->username,
							'firstname' => $cUser->name,
							'lastname' => $cUser->lastname,
							'profile_picture' => $cUser->profile_picture,
              'images' => $post->getPostImages(),
              'title' => $post->title,
              'content' => nl2br($post->content),
							'price' => $post->price,
              'category' => Category::find($post->category_id)->title,
							'category_id' => $post->category_id,
							//'num-of-messages' => PushMessage::where("post_id","=",$post->id)->count(),
							'likes' => $post->getLikes($cUser),
							'sold' => $post->sold
          ];
				}

				return $response;
		}

		else return $this->response->errorInternal();

	}

}

?>
