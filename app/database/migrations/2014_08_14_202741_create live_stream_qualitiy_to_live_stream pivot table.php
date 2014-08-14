<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveStreamQualitiyToLiveStreamPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('live_stream_qualitiy_to_live_stream', function(Blueprint $table)
		{
			$table->integer("live_stream_quality_id")->unsigned();
			$table->integer("live_stream_id")->unsigned();
			$table->primary(array("live_stream_quality_id", "live_stream_id"), "live_stream_quality_to_live_stream_primary");
			
			$table->timestamps();
			
			$table->foreign("live_stream_quality_id", "live_stream_quality_frn_key")->references('id')->on('live_streams_qualities')->onUpdate("restrict")->onDelete('restrict');
			$table->foreign("live_stream_id", "live_stream_id_frn_key")->references('id')->on('live_streams')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('live_stream_qualitiy_to_live_stream');
	}

}
