<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingDvrLiveStreamUris extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dvr_live_stream_uris', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("live_stream_uri_id")->unsigned();
			$table->integer("media_item_live_stream_id")->unsigned();
			$table->text("uri");
			$table->timestamps();
			
			$table->index("live_stream_uri_id");
			$table->foreign("live_stream_uri_id")->references('id')->on('live_stream_uris')->onUpdate("restrict")->onDelete('cascade');
			$table->index("media_item_live_stream_id");
			$table->foreign("media_item_live_stream_id")->references('id')->on('media_items_live_stream')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dvr_live_stream_uris');
	}

}
