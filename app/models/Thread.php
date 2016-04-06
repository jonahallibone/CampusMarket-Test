<?php

class Thread extends Eloquent {

  protected $fillable = array('post_id', 'user_id');
  protected $table = "threads";


  public static function check($userId, $postId) {
    //Check if thread exists for user
    //"num-of-messages" => PushMessage::where("post_id","=",$postId)
    if(Thread::where("user_id","=",$userId)->where("post_id","=",$postId)->exists()) {
      return true;
    }

    else return false;
  }

  public static function threads($userId) {
    if(Thread::where("user_id","=",$userId)->exists()) {
      return Thread::where("user_id","=",$userId)->get();
    }

    else return "No threads available for this user";
  }
}

?>
