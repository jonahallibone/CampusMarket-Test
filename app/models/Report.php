<?php
class Report extends Eloquent {

	protected $fillable = array('post_id', 'user_id', 'reported_user_id',"reason");

	protected $table = "reported_posts";

	public function posts() {
		return $this->belongsTo("Post");
	}

	public function user() {
		return $this->belongsTo("User");
	}

}

?>
