<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveInitialViewCountFromMediaItemsVideoAndTransferViews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$this->transferViews();
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->dropColumn("initial_view_count");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// not bothering to recalculate what the initial view count would have been because shouldn't need this anyway
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->integer("initial_view_count")->unsigned()->default(0);
		});
	}

	private function transferViews() {
		$rows = DB::table('media_items_video')->get();
		foreach($rows as $row) {
			$now = Carbon::now();
			$initialViewCount = intval($row->initial_view_count);
			for ($i=0; $i<$initialViewCount; $i++) {
				$newRecord = array(
					"session_id"	=> null,
					"user_id"		=> null,
					"type"			=> "vod",
					"media_item_id"	=> intval($row->media_item_id),
					"original_session_id"	=> "**initial**",
					"vod_source_file_id"	=> null,
					"playing"		=> true,
					"time"			=> null,
					"constitutes_view"	=> true,
					"created_at"	=> $now,
					"updated_at"	=> $now
				);
				DB::table('playback_history')->insert($newRecord);
			}
		}
	}
}
