<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeNameOptionalOnMediaItemsVideo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->dropColumn("name");
		});
		
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->string("name", 50)->nullable();
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
			$table->dropColumn("name");
		});
		
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->string("name", 50);
		});
	}

}
