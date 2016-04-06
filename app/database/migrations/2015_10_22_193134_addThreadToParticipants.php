<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class addThreadToParticipants extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table("private_threads", function($table){
			$table->dropColumn("to_user");
			$table->dropColumn("from_user");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table("private_threads", function($table){
			$table->integer("to_user");
			$table->integer("from_user");
		});
	}

}
