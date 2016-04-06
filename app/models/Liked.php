<?php
class Liked extends Eloquent {

	protected $fillable = array('post_id', 'user_id', 'is_liked');

	protected $table = "liked_posts";

	public function posts() {
		return $this->belongsTo("Post");
	}

	public function user() {
		return $this->belongsTo("User");
	}

}

?>
