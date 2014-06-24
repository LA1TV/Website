<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameEncodeStageToProcessStage extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vod_video_groups', function(Blueprint $table)
		{
			$table->renameColumn('encode_stage', 'process_stage');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vod_video_groups', function(Blueprint $table)
		{
			$table->renameColumn('process_stage', 'encode_stage');
		});
	}

}
