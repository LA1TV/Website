<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFullTextIndexOnMediaItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("CREATE FULLTEXT INDEX fulltext_index ON media_items (name, description)");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items', function(Blueprint $table)
		{
			$table->dropIndex("fulltext_index");
		});
	}

}
