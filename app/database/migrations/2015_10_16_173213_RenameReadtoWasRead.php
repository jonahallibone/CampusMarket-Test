<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameReadtoWasRead extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('push_messages', function($table) {
    	$table->renameColumn('`read`', 'was_read');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('push_messages', function($table) {
    	$table->renameColumn('was_read', '`read`');
		});
	}

}
