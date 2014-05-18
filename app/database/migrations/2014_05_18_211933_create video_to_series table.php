<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoToSeriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_to_series', function(Blueprint $table)
		{
			$table->integer("video_id")->unsigned();
			$table->integer("series_id")->unsigned();
			$table->primary(array("video_id", "series_id"));
			$table->smallInteger("position")->unsigned();
			$table->timestamps();
			
			$table->index("video_id");
			$table->index("series_id");
			
			$table->foreign("video_id")->references('id')->on('videos')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("series_id")->references('id')->on('series')->onUpdate("restrict")->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_to_series');
	}

}
