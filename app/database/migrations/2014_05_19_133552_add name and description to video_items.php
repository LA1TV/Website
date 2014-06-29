<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameAndDescriptionToVideoItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_items', function(Blueprint $table)
		{
			$table->string('name', 50);
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
		Schema::table('video_items', function(Blueprint $table)
		{
			$table->dropColumn('name');
			$table->dropColumn('description');
		});
	}

}
