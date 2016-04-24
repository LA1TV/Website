<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInheritedLiveMediaItemIdToLiveStreams extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_streams', function(Blueprint $table)
		{
			$table->integer("inherited_live_media_item_id")->unsigned()->nullable();
			$table->foreign("inherited_live_media_item_id", "inherited_live_media_item_id_frn")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('live_streams', function(Blueprint $table)
		{
			$table->dropForeign("inherited_live_media_item_id_frn");
			$table->dropColumn("inherited_live_media_item_id");
		});
	}

}
