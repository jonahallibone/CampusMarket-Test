<?php
class VerifyController extends BaseController {

public function index() {
  return "Welcome";
}

public function email() {
  
  $toReturn = 0;

  if(User::where('email', '=', Input::get('email'))->exists()) {
    $toReturn = 1;
  }

  return $toReturn;

}

public function username() {

  $toReturn = 0;

  if(User::where('username', '=', Input::get('username'))->exists()) {
    $toReturn = 1;
  }

  return $toReturn;

}

}
?>
