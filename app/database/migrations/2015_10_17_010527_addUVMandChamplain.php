<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUVMandChamplain extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('schools')->insert(
    	array('school_name' => 'Champlain College', 'area_id' => 1)
		);
		DB::table('schools')->insert(
    	array('school_name' => 'University of Vermont', 'area_id' => 1)
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
