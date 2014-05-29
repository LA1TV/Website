<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePlaylistsTableToWorkWithNewSeasonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('playlists', function(Blueprint $table)
		{
			$table->dropColumn("is_series");
			$table->dropColumn("name");
		});
		
		Schema::table('playlists', function(Blueprint $table)
		{
			$table->integer("series_id")->unsigned()->nullable();
			$table->tinyInteger("series_no")->unsigned()->nullable();
			$table->string("name", 50)->nullable();
			
			$table->index("series_id");
			
			$table->foreign("series_id")->references('id')->on('series')->onUpdate("restrict")->onDelete('restrict');
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
			$table->dropForeign('playlists_series_id_foreign');
			$table->dropColumn("series_id");
			$table->dropColumn("series_no");
			$table->dropColumn("name");
		});
		Schema::table('playlists', function(Blueprint $table)
		{
			$table->string("name", 50);
			$table->boolean("is_series");
		});
	}

}
