<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixVideosDescriptionType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('videos', function(Blueprint $table)
		{
			$table->dropColumn('description');
		});
		Schema::table('videos', function(Blueprint $table)
		{
			$table->text('description')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('videos', function(Blueprint $table)
		{
			$table->dropColumn('description');
		});
		Schema::table('videos', function(Blueprint $table)
		{
			$table->string('description')->nullable();
		});
	}

}
