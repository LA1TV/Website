<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveEncodeStageFromMediaItemsVideo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->dropColumn("encode_stage");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->tinyInteger('encode_stage')->unsigned()->default(0);
		});
	}

}
