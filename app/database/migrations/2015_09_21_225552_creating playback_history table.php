<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingPlaybackHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('playback_history', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string("session_id", 255)->nullable();
			$table->integer("user_id")->unsigned()->nullable();
			$table->string("type", 100);
			$table->integer("media_item_id")->unsigned();
			$table->string("original_session_id", 255);
			$table->integer("vod_source_file_id")->unsigned()->nullable();
			$table->boolean("playing");
			$table->integer("time")->unsigned()->nullable();
			$table->boolean("constitutes_view")->defaults(false);
			$table->timestamps();
			
			$table->index("original_session_id");
			// when watching now count by media item is calcuulated, it is filtered by entries > created_at
			// Without the index this query will look at all rows
			$table->index("created_at");
			$table->foreign("session_id")->references('id')->on('sessions')->onUpdate("restrict")->onDelete('set null');
			$table->foreign("user_id")->references('id')->on('site_users')->onUpdate("restrict")->onDelete('set null');
			$table->foreign("media_item_id")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("vod_source_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('playback_history');
	}

}
