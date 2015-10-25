<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingSearchIndexVersionAndCurrentSearchIndexVersionToShows extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('shows', function(Blueprint $table)
		{
			$table->integer("pending_search_index_version")->unsigned()->default(1);
			$table->integer("current_search_index_version")->unsigned()->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('shows', function(Blueprint $table)
		{
			$table->dropColumn("pending_search_index_version");
			$table->dropColumn("current_search_index_version");
		});
	}

}
