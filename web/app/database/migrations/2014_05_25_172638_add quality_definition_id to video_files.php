<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQualityDefinitionIdToVideoFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->integer("quality_definition_id")->unsigned();
			
			$table->index("quality_definition_id");
			
			$table->foreign("quality_definition_id")->references('id')->on('quality_definitions')->onUpdate("restrict")->onDelete('restrict');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->dropForeign('video_files_quality_definition_id_foreign');
			$table->dropColumn("quality_definition_id");
		});
	}

}
