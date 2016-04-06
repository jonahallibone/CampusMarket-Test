<?php
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
class MessagesApiController extends BaseController
{
    /**
     * Just for testing - the user should be logged in. In a real
     * app, please use standard authentication practices
     */
    public function __construct()
    {

    }
    /**
     * Show all of the message threads to the user
     *
     * @return mixed
     */
    public function index()
    {
        JWTAuth::parseToken();
        $user = JWTAuth::parseToken()->toUser();

        $currentUserId = $user->id;
        // All threads, ignore deleted/archived participants
        // $threads = Thread::getAllLatest()->get();
        // All threads that user is participating in
        $threads = Thread::forUser($currentUserId)->latest('updated_at')->get();
        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages($currentUserId)->latest('updated_at')->get();

        return compact('threads', 'currentUserId');
    }
    /**
     * Shows a message thread
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {

        $response = "";
        $isInThread = false;

        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return (":( Thread does not exist.");
        }



        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();
        // don't show the current user in list
        JWTAuth::parseToken();
        $user = JWTAuth::parseToken()->toUser();
        $userId = $user->id;
        //Return the users in the thread
        $users = User::whereIn('id', $thread->participantsUserIds())->get();

        foreach($users as $user) {
          if($user->id == $userId) {
            $isInThread = true;
          }
        }

        if($isInThread) {
        //if(true) {
        $thread->markAsRead($userId);
        foreach($thread->messages as $message) {
          $messages = [
            $theMessage = [
                'body' => $message->body,
                'timeStamp' => $message->created_at->diffForHumans(),
                'user' => $message->user_id
              ]
          ];
        }
        $response = compact('thread', 'users', 'messages');
      }

      else {
        throw new Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('You do not have permission to see that');
      }
        return $response;
    }
    /**
     * Creates a new message thread
     *
     * @return mixed
     */
    public function create()
    {
        JWTAuth::parseToken();
        $user = JWTAuth::parseToken()->toUser();
        $currentUserId = $user->id;
        $users = User::where('id', '!=', $currentUserId)->get();

    }
    /**
     * Stores a new message thread
     *
     * @return mixed
     */
    public function store()
    {

        JWTAuth::parseToken();
        $user = JWTAuth::parseToken()->toUser();
        $currentUserId = $user->id;
        $input = Input::all();
        $thread = Thread::create(
            [
                'subject' => $input['subject'],
            ]
        );
        // Message
        Message::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => $currentUserId,
                'body'      => $input['message'],
            ]
        );
        // Sender
        Participant::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => $currentUserId,
                'last_read' => new Carbon
            ]
        );
        // Recipients
        if (Input::has('recipients')) {
            $thread->addParticipants($input['recipients']);
        }
        Pusherer::trigger('new-thread-'.$input['recpients'], 'new-thread', array( 'message' => "A new thread was created" ));
        return $thread->id;
    }
    /**
     * Adds a new message to a current thread
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
            return Redirect::to('messages')->with('title', 'Messages');
        }
        $thread->activateAllParticipants();
        // Message
        Message::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => Auth::id(),
                'body'      => Input::get('message'),
            ]
        );
        // Add replier as a participant
        $participant = Participant::firstOrCreate(
            [
                'thread_id' => $thread->id,
                'user_id'   => Auth::user()->id
            ]
        );
        $participant->last_read = new Carbon;
        $participant->save();
        // Recipients
        if (Input::has('recipients')) {
            $thread->addParticipants(Input::get('recipients'));
        }
        return Redirect::to('messages/' . $id)->with('title','Thread');
    }
}
