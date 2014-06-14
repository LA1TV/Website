<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveMediaItemsVideoIdFromVideoFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->dropForeign('video_files_media_items_video_id_foreign');
			$table->dropColumn("media_items_video_id");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->integer('media_items_video_id')->unsigned();
			
			$table->index("media_items_video_id");
			
			$table->foreign("media_items_video_id")->references('id')->on('media_items_video')->onUpdate("restrict")->onDelete('cascade');

		});
	}

}
