<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShownAsLivestreamToLivestreams extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_streams', function(Blueprint $table)
		{
			$table->boolean("shown_as_livestream")->default(false);
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
			$table->dropColumn("shown_as_livestream");
		});
	}

}
