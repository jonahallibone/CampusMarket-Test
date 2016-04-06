<?php

class PushMessage extends Eloquent {
  protected $fillable = array('body', 'soft_delete', 'user_id', 'post_id');
  protected $table = 'push_messages';


  /*
  Returns the post_id of a Post that a user is talking in.
  Currently Untested.
  */
  public function getThreads($user) {



    $returnArray = [];
    $threads = Threads::where("user_id","=",$user->id)->get();


    foreach($threads as $thread) {
      $returnArray = $thread->post_id;
    }

    return $returnArray;

  }

}
