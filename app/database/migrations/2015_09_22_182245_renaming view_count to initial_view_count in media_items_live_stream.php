<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamingViewCountToInitialViewCountInMediaItemsLiveStream extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->integer('initial_view_count')->unsigned()->default(0);
		});
		$this->transfer("initial_view_count", "view_count");
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->dropColumn("view_count");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->integer('view_count')->unsigned()->default(0);
		});
		$this->transfer("view_count", "initial_view_count");
		Schema::table('media_items_live_stream', function(Blueprint $table)
		{
			$table->dropColumn("initial_view_count");
		});
	}

	private function transfer($to, $from) {
		$rows = DB::table('media_items_live_stream')->get();
		foreach($rows as $row) {
			DB::table('media_items_live_stream')->where('id', $row->id)->update([$to => $row->$from]);
		}
	}

}
