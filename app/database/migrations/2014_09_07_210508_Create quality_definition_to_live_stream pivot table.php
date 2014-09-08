<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQualityDefinitionToLiveStreamPivotTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('quality_definition_to_live_stream', function(Blueprint $table)
		{
			$table->integer("quality_definition_id")->unsigned();
			$table->integer("live_stream_id")->unsigned();
			$table->primary(array("quality_definition_id", "live_stream_id"), "quality_definition_id_to_live_stream_primary");
			
			$table->foreign("quality_definition_id", "quality_definition_frn_key_in_quality_definition_to_live_stream")->references('id')->on('quality_definitions')->onUpdate("restrict")->onDelete('restrict');
			$table->foreign("live_stream_id", "live_stream_id_frn_key_in_quality_definition_to_live_stream")->references('id')->on('live_streams')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('quality_definition_to_live_stream');
	}

}
