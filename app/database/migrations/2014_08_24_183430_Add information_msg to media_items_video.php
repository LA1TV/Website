<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInformationMsgToMediaItemsVideo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->text("information_msg")->nullable();
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
			$table->dropColumn("information_msg");
		});
	}

}
