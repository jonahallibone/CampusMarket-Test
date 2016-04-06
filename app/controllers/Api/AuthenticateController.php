<?php


use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends BaseController
{

    public function authenticate()
    {
        // grab credentials from the request
        if(Input::get('username')) {
          $credentials = Input::only('username', 'password');
          try {
              // attempt to verify the credentials and create a token for the user
              if (! $token = JWTAuth::attempt($credentials,['username','password'])) {
                  return $this->response->errorUnauthorized();
              }
          } catch (JWTException $e) {
              // something went wrong whilst attempting to encode the token
              return $this->response->errorInternal();
          }
        }

        else {
          $credentials = Input::only('email', 'password');
          try {
              // attempt to verify the credentials and create a token for the user
              if (! $token = JWTAuth::attempt($credentials)) {
                  return $this->response->errorUnauthorized();
              }
          } catch (JWTException $e) {
              // something went wrong whilst attempting to encode the token
              return $this->response->errorInternal();
          }
        }

        Auth::once($credentials);

        $active = Auth::user()->active;

        if(!$active) {
          return $this->response->errorUnauthorized();
        }

        $thetoken = new stdClass();
        $thetoken->token = $token;
        return $this->response->item($thetoken, new TokenTransformer)->withHeader('Access-Control-Allow-Origin','*');
    }

    public function getRequest() {
      return $this->response->errorNotFound();
    }
}

?>
