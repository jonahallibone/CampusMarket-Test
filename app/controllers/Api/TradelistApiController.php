<?php
class TradelistApiController extends BaseController {

  public function index() {
    $user = JWTAuth::parseToken()->toUser();

    $list = Tradelist::where("user_id","=",$user->id)->select("id","body")->get();

    return $list;

  }

  public function create() {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();
    $item;

    if($item = Input::get("item")) {
      $newItem = Tradelist::create(array(
        "user_id" => $user->id,
        "body" => $item
      ));

      if($newItem->save()) {
        $list = Tradelist::where("user_id","=",$user->id)->get();

        $id = $newItem->id;
        $body = $newItem->body;
        return compact("id","body");
      }
      else return "Item was not able to be added.";
    }

    else return "No item to be added";

  }

  public function show($id) {
    $userId = User::findOrFail($id);
    $list = Tradelist::where("user_id","=",$id)->get();
    return $list;
  }

  public function delete($id) {
    JWTAuth::parseToken();
    $user = JWTAuth::parseToken()->toUser();

    $item = Tradelist::findOrFail($id);

    if($item->user_id == $user->id) {
      if($item->delete()) {
        $list = Tradelist::where("user_id","=",$user->id)->get();
        return $list;
      }

      else return "Could not be deleted";
    }

    else return "There was an error";
  }
}
?>
