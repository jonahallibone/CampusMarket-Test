<?php

use Nicolaslopezj\Searchable\SearchableTrait;

class Post extends Eloquent {

	 use SearchableTrait;

		//End Search settings

	protected $fillable = array('title', 'content', 'posted_by', 'category_id','price','soft_delete');

	protected $table = "posts";


	/**
      * Searchable rules.
      *
      * @var array
      */
     protected $searchable = [
         'columns' => [
             'title' => 10,
             'content' => 10,
             'posted_by' => 5,
             'price' => 2,
         ]
     ];


	public function categories() {
      return $this->hasOne('Category'); // this matches the Eloquent model
  }

  public function pictures() {
  	return $this->hasMany('Picture');
  }

	public function liked() {
		return $this->hasMany("Liked");
	}

  public function getPostImages() {
		return Picture::where('post_id','=',$this->id)->get();
	}

	public function cleanImages($key) {

		$toClean = Picture::where('temp_uuid','!=',$key)
							->where("temp_uuid","!=",1)
							->where("user_id","=",$this->posted_by)
							->get();

		foreach ($toClean as $cleanable) {
			File::delete($cleanable->path);
			$cleanable->delete();
		}
	}

	public function getLikes($user) {
		$likes = Liked::where("post_id","=", $this->id)
		->where("is_liked","=",1)
		->count();

		$isLiked = false;

		if(Liked::where("user_id","=",$user->id)
							->where("post_id","=", $this->id)
							->where("is_liked","=",1)
							->exists()) {
									$isLiked = true;
		}

		return compact("likes", "isLiked");
	}

	public function sold() {
		$this->sold = 1;
		$this->save();
	}

	public function likes($user) {

		$liked = Liked::where("user_id","=",$user->id)->where("post_id","=",$this->id);

		if($liked->exists()) {
			$fliked = $liked->first();
			if($fliked->is_liked == 1) {
				$fliked->is_liked = 0;
				$fliked->save();
				return true;
			}

			else if($fliked->is_liked == 0) {
				$fliked->is_liked = 1;
				$fliked->save();
				return true;
			}
		}

		else if(!$liked->exists()) {
			$like = Liked::create(array(
				"user_id" => $user->id,
				"post_id" => $this->id,
				"is_liked" => 1
			));

			if($like->save()) {
				return true;
			}

		}

		else return false;
	}

	public static function getPosts($user) {
		$posts = Post::where("posted_by","=",$user->id)->where("soft_delete","!=",1)->get();

		$response = [
			'posts'  => []
		];

		foreach($posts as $post){
			$response['posts'][] = [
					'id' => $post->id,
					'user_id' => $post->posted_by,
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
					'likes' => $post->getLikes($user)
			];
		}

		return $response;
	}


}



?>
