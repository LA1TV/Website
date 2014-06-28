<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CorrectNameFieldType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_streams_qualities', function(Blueprint $table)
		{
			$table->dropColumn("name");
		});
		Schema::table('live_streams_qualities', function(Blueprint $table)
		{
			$table->string('name', 50);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('live_streams_qualities', function(Blueprint $table)
		{
			$table->dropColumn("name");
		});
		Schema::table('live_streams_qualities', function(Blueprint $table)
		{
			$table->text('name', 50);
		});
	}

}
