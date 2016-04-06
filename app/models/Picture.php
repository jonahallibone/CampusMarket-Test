<?php

class Picture extends Eloquent {



	protected $fillable = array('path','temp_uuid','user_id','post_id');

	protected $table = "post_images";

}



?>
