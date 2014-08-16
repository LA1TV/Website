<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionToQualityDefinitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('quality_definitions', function(Blueprint $table)
		{
			$table->tinyInteger("position")->unique("position_unique_index");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('quality_definitions', function(Blueprint $table)
		{
			$table->dropColumn("position");
		});
	}

}