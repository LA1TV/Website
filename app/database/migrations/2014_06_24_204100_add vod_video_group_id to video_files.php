<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVodVideoGroupIdToVideoFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->integer("vod_video_group_id")->unsigned();
			
			$table->index("vod_video_group_id");
			$table->foreign("vod_video_group_id", "vod_video_group_id_foreign")->references('id')->on('vod_video_groups')->onUpdate("restrict")->onDelete('cascade');
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
			$table->dropForeign("vod_video_group_id_foreign");
			$table->dropColumn("vod_video_group_id");
		});
	}

}
