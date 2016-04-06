<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateUsersTable extends Migration {

public function up()
	{
		Schema::create('users', function($table)
    	{
		 	$table->increments('id');
        	$table->string('email')->unique();
        	$table->string('first-name');
        	$table->string('last-name');
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
	}	//

}

?>
