<?php
class Reset extends Eloquent {

	protected $fillable = array('email', 'token');

	protected $table = "password_reminders";

}

?>
