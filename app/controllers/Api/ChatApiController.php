<?php
class ChatApiController extends BaseController {


  public function index() {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    $threads = Thread::threads($user->id);

    return $threads;

  }


  public function show($postId) {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    $theMessages = [];

    if($user) {
      $messages = PushMessage::where("post_id","=",$postId)->orderBy('created_at', 'desc')->paginate(20);
      foreach($messages as $message) {
        $theMessages[] = [
           'message' => $message->body,
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
    return $theMessages;
  }

 public function store() {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    $postId = Input::get('post-id');
    $message = Input::get('message');

    if(!Thread::check($user->id, $postId)) {
      $nThread = Thread::create(array(
        "user_id" => $user->id,
        "post_id" => $postId
      ));
      $nThread->save();
    }

    $newMessage = PushMessage::create(
      array(
        "body" => $message,
        "post_id" => $postId,
        "soft_delete" => 0,
        "user_id" => $user->id
      ));

      $newMessage->save();

      Pusherer::trigger(strval($postId), 'new-message', array(
         'message' => $newMessage->body,
         'user' => array(
           "user-firstname" => User::find($newMessage->user_id)->name,
           "user-lastname" => User::find($newMessage->user_id)->lastname,
           "user-username" => User::find($newMessage->user_id)->username,
           "user-id" => $newMessage->user_id,
           "profile-picture" => User::find($newMessage->user_id)->profile_picture,
           "updated" => $newMessage->updated_at
         )
       ));
  }
}
?>
