<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveViewCountFromMediaItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items', function(Blueprint $table)
		{
			$table->dropColumn("view_count");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items', function(Blueprint $table)
		{
			$table->integer('view_count')->unsigned()->default(0);
		});
	}

}
