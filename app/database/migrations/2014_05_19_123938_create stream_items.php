<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stream_items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean("enabled");
			$table->integer('media_item_id')->unsigned();
			$table->integer('live_stream_id')->unsigned()->nullable();
			$table->timestamps();
			
			$table->index("media_item_id");
			$table->index("live_stream_id");
			
			$table->foreign("media_item_id")->references('id')->on('videos')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("live_stream_id")->references('id')->on('live_streams')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stream_items');
	}

}
