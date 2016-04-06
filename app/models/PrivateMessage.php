<?php

class PrivateMessage extends Eloquent {

  protected $fillable = array('message','thread_id','user_id');

	protected $table = "private_messages";


  public static function getMessages($messages) {

    $toReturn = [];

    if($messages) {
      foreach($messages as $message) {
        $toReturn[] = [
          'message' => $message->message,
          'user' => array(
            "user-firstname" => User::find($message->user_id)->name,
            "user-lastname" => User::find($message->user_id)->lastname,
            "user-username" => User::find($message->user_id)->username,
            "user-id" => $message->user_id,
            "profile-picture" => User::find($message->user_id)->profile_picture,
            "updated" => $message->updated_at
          )
        ];
      }
    }
    return $toReturn;
  }

  public function triggerPusher($message, $userId) {

    Pusherer::trigger("private-" . strval($message->thread_id), 'new-private-message', array(
       'message' => $message->message,
       'user' => array(
         "user-firstname" => User::find($message->user_id)->name,
         "user-lastname" => User::find($message->user_id)->lastname,
         "user-username" => User::find($message->user_id)->username,
         "user-id" => $message->user_id,
         "profile-picture" => User::find($message->user_id)->profile_picture,
         "updated" => $message->updated_at
       )
     ));

     Pusherer::trigger("private-" . strval($userId) , 'private-message-alert', array(
        'message' => $message->message,
        'user' => array(
          "user-firstname" => User::find($message->user_id)->name,
          "user-lastname" => User::find($message->user_id)->lastname,
          "user-username" => User::find($message->user_id)->username,
          "user-id" => $message->user_id,
          "profile-picture" => User::find($message->user_id)->profile_picture,
          "thread_id" => $message->thread_id
        )
      ));
  }
}
?>
