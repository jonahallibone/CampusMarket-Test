<?php

class HomeController extends BaseController {



	public function home() {
			  return File::get(public_path() . '/index.html');
			}
}

?>
