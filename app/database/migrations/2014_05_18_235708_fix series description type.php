<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSeriesDescriptionType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('series', function(Blueprint $table)
		{
			$table->dropColumn('description');
		});
		Schema::table('series', function(Blueprint $table)
		{
			$table->text('description')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('series', function(Blueprint $table)
		{
			$table->dropColumn('description');
		});
		Schema::table('series', function(Blueprint $table)
		{
			$table->string('description')->nullable();
		});
	}

}
