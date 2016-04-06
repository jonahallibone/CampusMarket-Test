<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAndThreadToPMessahes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table("private_messages", function($table){
			$table->integer("thread_id");
			$table->integer("user_id");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table("private_messages", function($table){
			$table->dropColumn("thread_id");
			$table->dropColumn("user_id");
		});
	}

}
