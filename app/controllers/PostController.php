<?php


	class PostController extends BaseController {


		public function publishPost() {
			$user = Auth::user();
			$fileProcessing = false;
			$key = Input::get('_uuid');

			if(Input::hasFile('file-upload')) {

				$fileProcessing = true;

				$files = Input::file('file-upload');

				foreach ($files as $file) {

					$img = Image::make($file);
					//if larger than 1920, make smaller
					$img->widen(1920, function ($constraint) {
    					$constraint->upsize();
					});
					//if taller than 1080, make smaller than 1080
					$img->heighten(1080, function ($constraint) {
    					$constraint->upsize();
					});


					$fileName = Uuid::generate();
					$extenstion = $file->getClientOriginalExtension();
					$fileItself = $fileName . '.' . $extenstion;

					$path = "/uploads/" . $fileItself;

					//Working
					$img->save(public_path($path));

					Picture::create(array(
						"temp_uuid" => $key,
						"path" => $path,
						"user_id" => $user->id
					));

				}
			}

			if(!$fileProcessing) {
				//Validation
				$validator = Validator::make(Input::all(),
					array(
						'item-title' => 'unique:posts,title|required|max:50|min:1',
						'item-price' => 'required|min:1',
						'item-description' => 'required|min:1|max:500',
						'category' => 'required|numeric|max:16',
						'file-upload' => 'image'
					)
				);


				if($validator->fails()) {
					return Redirect::route('home')
						-> withErrors($validator)
						-> withInput();
				}

				else {

					$category = Input::get('category');

					$thePost = Post::create(array(
						'title' => Input::get('item-title'),
						'price' => Input::get('item-price'),
						'content' => Input::get('item-description'),
						'category_id' => $category,
						'posted_by' => $user->id
					));

					$thePost->save();

					$postImages = Picture::where("temp_uuid", "=", $key)->get();

					foreach($postImages as $image) {
						$image->post_id = $thePost->id;
						$image->temp_uuid = 1;
						$image->save();
					}

					$thePost->cleanImages($key);

					return Redirect::route("home");
				}
			}
		}


	}

?>
