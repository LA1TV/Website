<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLikesEnabledAndCommentsEnabledToMediaItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items', function(Blueprint $table)
		{
			$table->boolean("likes_enabled")->default(true);
			$table->boolean("comments_enabled")->default(true);
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
			$table->dropColumn("likes_enabled");
			$table->dropColumn("comments_enabled");
		});
	}

}
