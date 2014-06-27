<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveEncodeStageAndVodVideoGroupIdFromVideoFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->dropForeign("vod_video_group_id_foreign");
			
			$table->dropColumn("encode_stage");
			$table->dropColumn("vod_video_group_id");
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
			$table->index("vod_video_group_id");
			$table->foreign("vod_video_group_id", "vod_video_group_id_foreign")->references('id')->on('vod_video_groups')->onUpdate("restrict")->onDelete('cascade');

			$table->integer("vod_video_group_id")->unsigned();
			$table->tinyInteger("encode_stage")->unsigned()->default(0);
		});
	}

}
