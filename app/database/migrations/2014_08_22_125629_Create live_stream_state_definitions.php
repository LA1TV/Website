<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveStreamStateDefinitions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('live_stream_state_definitions', function(Blueprint $table)
		{
			$table->integer("id")->unsigned();
			$table->string("name", 50);
			$table->timestamps();
			
			$table->primary('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('live_stream_state_definitions');
	}

}
