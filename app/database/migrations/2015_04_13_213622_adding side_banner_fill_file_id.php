<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingSideBannerFillFileId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('playlists', function(Blueprint $table)
		{
			$table->integer("side_banner_fill_file_id")->unsigned()->nullable();
			
			$table->index("side_banner_fill_file_id");
			
			$table->foreign("side_banner_fill_file_id", "playlists_side_banner_fill_file_id_frn")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
		
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('playlists', function(Blueprint $table)
		{
			$table->dropForeign("playlists_side_banner_fill_file_id_frn");
			$table->dropColumn("side_banner_fill_file_id");
		});
	}

}
