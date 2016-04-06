<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("reported_posts", function($table) {
			$table->increments("id");
			$table->integer("post_id");
			$table->integer("user_id");
			$table->integer("reported_user_id");
			$table->string("reason");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("reported_posts");
	}

}
