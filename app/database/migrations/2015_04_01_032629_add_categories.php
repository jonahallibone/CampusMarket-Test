<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('categories')->insertGetId(array(
			'title' => "Men's clothing",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Women's clothing",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Unisex clothing",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Hats",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Misc",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Local",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Long Distance",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Textbooks",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Leisure",
		));


		DB::table('categories')->insertGetId(array(
			'title' => "Seeking - Internships",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Seeking - Part Time",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Hiring - Internships",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Hiring - Part Time",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Lost and Found",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Electronics",
		));

		DB::table('categories')->insertGetId(array(
			'title' => "Tutoring and Study Groups",
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		
	}

}
