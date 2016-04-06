<?php

class ProfileController extends BaseController {


	public function getProfile($accountName) {

			//Set up variables for the user
			$email = "";
	 		$username = "";
	 		$firstname = "";
	 		$lastname = "";
	 		$isOwner = false;

			//Login Check to see if it is their profile

			if(Auth::check()) {
				if(User::where('username', '=', $accountName)->where('active','=',1)->exists()) {
					$user = Auth::user();
					//If the username matches the url name and it's the current user
					if($user->username == $accountName) {
						$email = $user->email;
						$username = $user->username;
						$firstname = $user->name;
						$lastname = $user->lastname;

						return View::make('profile.profile-editable', array(
									'firstname' => $firstname,
									'lastname'=> $lastname,
									'email' => $email,
									'username' => $username
									)
						)->with('title', $firstname . "'s profile");

					}
					//If the username exists, but is not the one owned by the user
					else {
						$thing = User::where('username','=', $accountName)->first();

						$email = $thing->email;
						$username = $thing->username;
						$firstname = $thing->firstname;
						$lastname = $thing->lastname;

						return "<b>This is not your account <br>
								User found<br>
								User email: $email <br>
								User's name: $firstname<br>
								User's last name: $lastname<br>
								User's username: $username<br></b>";

					}
				}
				//No such user
				else {
					return "<b>There is no such user</b>";
				}

			}
	}

}
?>
