<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table) {
    		$table->integer('grad_class');
    		$table->string('user_bio');
				$table->string('school');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('grad_class');
		$table->dropColumn('user_bio');
		$table->dropColumn('school');
	}

}
