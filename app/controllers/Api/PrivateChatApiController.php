<?php

use \Pusher;

class PrivateChatApiController extends BaseController {

  public function index() {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    $threads = PrivateParticipants::getThreads($user->id);

    return $threads;
  }

  public function show($threadId) {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();
    if(PrivateThread::where("id","=",$threadId)->exists()) {
      if(PrivateParticipants::check($user->id, $threadId)) {
        if($messages = PrivateMessage::getMessages(PrivateMessage::where("thread_id","=",$threadId)->orderBy('created_at','desc')->paginate(10))) {
          $oQuery = PrivateParticipants::where("thread_id","=",$threadId)->where("user_id","!=",$user->id)->first();
          if($oQuery) {
            $userGot = User::where("id","=", $oQuery->user_id)->select("id","name","lastname")->first();
            //Other user array for the Obj
            $otherUser = [
              "id" => $userGot->id,
              "name" => $userGot->name,
              "lastname" => $userGot->lastname
            ];

            return compact("otherUser", "messages");
          }
          else return $this->response->errorInternal();
        }
        else return $this->response->errorInternal();
      }
      else return $this->response->errorForbidden();
    }
    else return $this->response->errorNotFound();
  }

  public function store() {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    //If there is a thread ID sent
    if(Input::get("thread-id")) {
      //Get the thread ID
      $threadId = Input::get("thread-id");
      //If that thread exists
      if(PrivateThread::doesThreadExist($threadId)) {
        //Check to see if the user is in that thread
        if(PrivateParticipants::check($user->id, $threadId)) {
          //If so, add a message
          $newMessage = PrivateMessage::create(array(
            "thread_id" => $threadId,
            "user_id" => $user->id,
            "message" => Input::get("message")
          ));
          //If the new message saves, return the new message
          if($newMessage->save()) {
            $toUser = PrivateParticipants::where("thread_id","=",$threadId)->where("user_id","!=",$user->id)->get();
            $newMessage->triggerPusher($newMessage, $toUser[0]->user_id);
            return $newMessage;
          }
        }
        //If the user wasnt in the thread, return a 401
        else return $this->response->errorForbidden();
      }
    }

    else if(Input::get("to-user")){
        $toUser = Input::get("to-user");
      //If new user exists in the DB
        if(User::where("id","=",$toUser)->exists() && PrivateParticipants::checkForThread($user->id, $toUser)) {

        $newThread; //Initialize newThread
        //Check to make sure a new thread can be and was created
        if($newThread = PrivateThread::create(array())) {
          //Add participant one
          $nParticipantOne = PrivateParticipants::create(array(
            "user_id" => $user->id,
            "thread_id" => $newThread->id
          ));
          //Add participant two
          $nParticipantTwo = PrivateParticipants::create(array(
            "user_id" => $toUser,
            "thread_id" => $newThread->id
          ));

          $nParticipantOne->save();
          $nParticipantOne->save();

          $newMessage = PrivateMessage::create(array(
            "thread_id" => $newThread->id,
            "user_id" => $user->id,
            "message" => Input::get("message")
          ));

          $newMessage->save();
          $newThread->save();
          $newMessage->triggerPusher($newMessage, $toUser);

          $toReturn = [
            'message' => $newMessage,
            'realmessage' => $newMessage->message,
            'user' => array(
              "user-firstname" => User::find($newMessage->user_id)->name,
              "user-lastname" => User::find($newMessage->user_id)->lastname,
              "user-username" => User::find($newMessage->user_id)->username,
              "user-id" => $newMessage->user_id,
              "profile-picture" => User::find($newMessage->user_id)->profile_picture,
              "thread_id" => $newThread->id,
              "updated" => $newMessage->updated_at
              )
            ];
          return $toReturn;

        }
        else return "Failed to make new thread!";
      }

      else if(!PrivateParticipants::checkForThread($user->id, $toUser)) {
        $getThread = PrivateParticipants::getThreadFromUsers($user->id, $toUser);
        $newMessage = PrivateMessage::create(array(
          "thread_id" => $getThread,
          "user_id" => $user->id,
          "message" => Input::get("message")
        ));

        $newMessage->save();
        $newMessage->triggerPusher($newMessage, $toUser);
        return $newMessage;
      }

      else return "User does not exist";
    }

    else return "No new user submitted";

  }

  public function auth() {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    $socket_id = Input::get("socket_id");
    $channel_name = Input::get("channel_name");
    $thread_id = explode("-", $channel_name);
    if($channel_name == "private-" . $user->id) {
      return Pusherer::socket_auth($channel_name, $socket_id);
    }

    else if(PrivateParticipants::check($user->id, $thread_id[1])) {
      return Pusherer::socket_auth($channel_name, $socket_id);
    }

    else return $this->response->errorForbidden();


  }

}

?>
