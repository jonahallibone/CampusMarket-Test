<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	Schema::create('users', function($table)
    	{
		 	$table->increments('id');
        	$table->string('email')->unique();
        	$table->string('firstname');
        	$table->string('lastname');
            $table->string('username')->unique();
        	$table->string('city');
        	$table->string('state');
        	$table->string('password');
        	$table->string('temp_password');
        	$table->string('code');
        	$table->integer('active');
        	$table->timestamps();
        });
	}

	
	public function down()
	{
		Schema::drop('users');
	}	

}
