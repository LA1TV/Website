<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoverArtFileIdToMediaItemsVideo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->integer("cover_art_file_id")->unsigned()->nullable();
			
			$table->index("cover_art_file_id");
			
			$table->foreign("cover_art_file_id", "cover_art_file_id_frn")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
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
			$table->dropForeign("cover_art_file_id_frn");
			$table->dropColumn("cover_art_file_id");
		});
	}

}
