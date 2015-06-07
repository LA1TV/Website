<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWatchingNowTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('watching_now', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("media_item_id")->unsigned();
			$table->string("session_id", 255);
			$table->timestamps();
			
			$table->foreign("media_item_id")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("session_id")->references('session_id')->on('users_sessions')->onUpdate("restrict")->onDelete('cascade');
			$table->unique(array("media_item_id", "session_id"));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('watching_now');
	}

}
