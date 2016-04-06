<?php
class PrivateThread extends Eloquent {

  protected $table = "private_threads";

  public static function doesThreadExist($threadId) {

    if(PrivateThread::where("id","=",$threadId)->exists()) {
      return true;
    }

    else return false;
  }
}

?>
