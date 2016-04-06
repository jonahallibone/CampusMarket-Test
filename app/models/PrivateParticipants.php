<?php

use Carbon\Carbon;

class PrivateParticipants extends Eloquent {

  protected $fillable = array("user_id", "thread_id");
  protected $table = "private_participants";


  public static function getThreads($userId) {
    $toReturn = [];

    $threads = PrivateParticipants::where("user_id","=",$userId)->get();

    if (count($threads) > 0) {
      foreach($threads as $thread) {
        $recentMessageQ = PrivateMessage::where("thread_id","=",$thread->thread_id)->select("message","created_at")->orderBy('created_at', 'desc')->first();

        $toReturn[] = [
            "thread_id" => $thread->thread_id,
            "participants" => PrivateParticipants::getParticipants($thread->thread_id),
            "recent_message" => $recentMessageQ->message,
            "recent_message_time" => $recentMessageQ->created_at->diffForHumans()

          ];
      }
    }
    else $toReturn = "No available threads for this user";

    return $toReturn;

  }

  public static function getParticipants($threadId) {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    $toReturn = [];
    $participants = PrivateParticipants::where("thread_id","=",$threadId)->where("user_id","!=",$user->id)->get();

    foreach($participants as $participant) {
      $toReturn[] = [
          "user-name" => User::find($participant->user_id)->name,
          "user-lastname" => User::find($participant->user_id)->lastname,
          "user-id" => $participant->user_id,
          "profile-picture" => User::find($participant->user_id)->profile_picture
        ];
    }

    return $toReturn;
  }

  public static function check($userId, $threadId) {
    if(PrivateParticipants::where("user_id","=",$userId)->where("thread_id","=",$threadId)->exists()) {
      return true;
    }

    else return false;
  }

  public static function checkForThread($userone, $usertwo) {

    $theFirstThreads = PrivateParticipants::where("user_id","=",$userone)->get();
    $theSecondThreads = PrivateParticipants::where("user_id","=",$usertwo)->get();

    foreach($theFirstThreads as $threadone) {
      foreach($theSecondThreads as $threadtwo) {
        if($threadone->thread_id == $threadtwo->thread_id ) {
          return false;
        }
      }
    }
    return true;
  }

  public static function getThreadFromUsers($userone, $usertwo) {
    $theFirstThreads = PrivateParticipants::where("user_id","=",$userone)->get();
    $theSecondThreads = PrivateParticipants::where("user_id","=",$usertwo)->get();

    foreach($theFirstThreads as $threadone) {
      foreach($theSecondThreads as $threadtwo) {
        if($threadone->thread_id == $threadtwo->thread_id ) {
          return $threadone->thread_id;
        }
      }
    }
    return null;
  }

}

?>
