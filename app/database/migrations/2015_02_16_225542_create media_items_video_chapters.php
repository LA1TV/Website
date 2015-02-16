<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaItemsVideoChapters extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media_items_video_chapters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("media_item_id")->unsigned();
			$table->integer("time")->unsigned();
			$table->text("title");
			$table->timestamps();
			
			$table->foreign("media_item_id", "media_items_video_chapters_media_item_id_foreign")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('media_items_video_chapters');
	}

}
