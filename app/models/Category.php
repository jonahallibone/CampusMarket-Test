<?php

class Category extends Eloquent {

	protected $fillable = array('title', 'count');

	protected $table = "categories";

	public function post() {
		return $this->belongsTo('Post');
	}



}



?>