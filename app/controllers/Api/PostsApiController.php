<?php

class PostsApiController extends BaseController {
	public function index()
	{
		$cUser = JWTAuth::parseToken()->toUser();

    $response = [
      'posts'  => []
    ];

		$posts = "";
		$categories = "";

		if(Input::get("category") && !(Input::get("user-id"))) {
		$categories = Input::get("category");
		$posts = Post::where("soft_delete","!=","1")
						->whereIn("category_id",$categories)
						->where("sold","=",0)
						->where("reported","=",0)
						->orderBy('created_at', 'desc')
						->paginate(10);
		}
		else if (Input::get("user-id") && !(Input::get("category"))) {
			$posts = Post::where("soft_delete","!=","1")
							->where("posted_by","=", Input::get("user-id"))
							->where("reported","=",0)
							->orderBy('created_at', 'desc')
							->paginate(10);
		}
		else {
    	$posts = Post::where("soft_delete","!=","1")
											->where("sold","=",0)
											->where("reported","=",0)
											->orderBy('created_at', 'desc')
											->paginate(10);
		}

    foreach($posts as $post){
				$user = User::where("id","=",$post->posted_by)->first();
				if($user) {
          $response['posts'][] = [
              'id' => $post->id,
							'user_id' => $user->id,
              'username' => $user->username,
							'firstname' => $user->name,
							'lastname' => $user->lastname,
							'profile_picture' => $user->profile_picture,
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
    }

  	return $response;

	}


	public function store() {

			JWTAuth::parseToken();
			$user = JWTAuth::parseToken()->toUser();

			$paths = [];

			$oFiles = Input::get("item-image");

			foreach($oFiles as $oFile) {
				if($oFile!=="") {
					if($img = Image::make($oFile)) {
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

						$path = "https://s3.amazonaws.com/blackmarket-uploads/" . $fileItself;

						$paths[] = $path;
						$img->stream();

						//Moved off the disk!!!!!
						//To s3!!!!
						//$img->save(public_path($path));

						$s3 = AWS::get('s3');
						$s3->putObject(array(
						    'Bucket'     => 'blackmarket-uploads',
						    'Key'        => $fileItself,
						    'Body' 			 => $img->__toString(),
						));


					}
					else {
						return $this->response->errorInternal();
					}

			}

			else {
				return $this->response->errorInternal();
			}

		}

		if(Category::find(Input::get("category-id"))) {
			$thePost = Post::create(array(
					'title' => Input::get('item-title'),
					'price' => Input::get('item-price'),
					'content' => Input::get('item-description'),
					'category_id' => Input::get('category-id'),
					'posted_by' => $user->id,
			));

			$thePost->save();
		}

		else {
			return $this->response->errorInternal();
		}



		foreach($paths as $path) {
			$pic = Picture::create(array(
				"path" => $path,
				"user_id" => $user->id,
				"post_id" => $thePost->id
			));

			$pic->save();
		}

		$post = Post::find($thePost->id);
		$pUser = User::where("id","=",$post->posted_by)->first();

		return $rPost = [
				'id' => $post->id,
				'user_id' => $pUser->id,
				'username' => $pUser->username,
				'firstname' => $pUser->name,
				'lastname' => $pUser->lastname,
				'profile_picture' => $pUser->profile_picture,
				'images' => $post->getPostImages(),
				'title' => $post->title,
				'content' => nl2br($post->content),
				'price' => $post->price,
				'category' => Category::find($post->category_id)->title,
				'category_id' => $post->category_id,
				//'num-of-messages' => PushMessage::where("post_id","=",$post->id)->count(),
				'likes' => $post->getLikes($user),
				'sold' => $post->sold

		];

	}

	public function delete($id) {
		JWTAuth::parseToken();
		$user = JWTAuth::parseToken()->toUser();

		$thePost;
		$toReturn = 'access';
		$thePost = Post::find($id);

		if($thePost) {
			if($thePost->posted_by == $user->id) {
				$thePost->soft_delete = 1;
				$thePost->save();
				$toReturn = "Post successfully deleted";
			}

			else $toReturn = "Not your account";

		}

		else {
			$toReturn = "Sorry there was a problem deleting  your post";
		}

		return $toReturn;

	}

	public function show($id) {

	}

	public function search() {
		$cUser = JWTAuth::parseToken()->toUser();

		$response = [
			'posts'  => []
		];

		$query = Input::get("query");

		$posts = Post::where("soft_delete","=","0")
										->where("sold","=",0)
										->where("reported","=",0)
										->search($query)
										->paginate(10);

		foreach($posts as $post){
				$user = User::where("id","=",$post->posted_by)->first();
				if($user) {
          $response['posts'][] = [
              'id' => $post->id,
							'user_id' => $user->id,
              'username' => $user->username,
							'firstname' => $user->name,
							'lastname' => $user->lastname,
							'profile_picture' => $user->profile_picture,
              'images' => $post->getPostImages(),
              'title' => $post->title,
              'content' => nl2br($post->content),
							'price' => $post->price,
              'category' => Category::find($post->category_id)->title,
							'category_id' => $post->category_id,
							//'num-of-messages' => PushMessage::where("post_id","=",$post->id)->count(),
							'likes' => $post->getLikes($cUser)
          ];
				}
	    }

		return $response;


	}

	public function liked() {
		$user = JWTAuth::parseToken()->toUser();

		$postId = Input::get("post-id");
		if($thePost = Post::find($postId)) {
			if($thePost->likes($user)){
				return $this->response->noContent();
			}
			else return $this->response->errorInternal();
		}

		else return $this->response->errorInternal();
	}

	public function sold() {
		$user = JWTAuth::parseToken()->toUser();
		$postId = Input::get("post-id");
		if($thePost = Post::find($postId)) {
			if($thePost->posted_by === $user->id) {
				$thePost->sold();
				return $this->response->noContent();
			}
			else return $this->response->errorInternal();
		}
		else return $this->response->errorInternal();
	}

	public function edit() {
		$user = JWTAuth::parseToken()->toUser();

		$validator = Validator::make(Input::all(),
			array(
				'price' => 'required|numeric',
				'title' => 'required|min:1',
				'content' => 'required|min:2|max:1000',
				'post-id' => 'required|min:1|exists:posts,id'
			)
		);

		if($validator->fails()) {
			return $this->response->error('Error making account', 401);
		}

		else {
			$post = Post::find(Input::get("post-id"));
				if($post->posted_by === $user->id) {
				$post->content = Input::get("content");
				$post->price = Input::get("price");
				$post->title = Input::get("title");
				$post->save();
				$post->touch();
				$this->response->noContent();
			}
			else {
				return $this->response->errorInternal();
			}
		}
	}

	public function report() {
		$user = JWTAuth::parseToken()->toUser();

		$validator = Validator::make(Input::all(),
			array(
				'post_id' => "reqired|exists:posts,id",
				'reason' => "required"
			)
		);

		if($validator->fails()) {
			return $this->response->error('Error reporting post', 500);
		}

		if($post = Post::find(Input::get("post-id"))) {

			Report::create(array(
				"post_id" => $post->id,
				"user_id" => $user->id,
				"reported_user_id" => $post->posted_by,
				"reason" => Input::get("reason")
			));

			$post->reported = 1;
			$post->save();

			return $this->response->noContent();
		}
		else return $this->response->error('Error reporting post', 500);
	}

}

?>
