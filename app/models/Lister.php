<?php

class Lister extends Eloquent {

	//fillable for the database
	protected $fillable = array('email', 'college');

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'waiting_list';

}

?>
