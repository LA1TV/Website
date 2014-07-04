<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceFileIdProcessStageProcessPercentageErrorMsgToFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->integer("source_file_id")->unsigned()->nullable();
			$table->tinyInteger("process_stage")->unsigned()->default(0);
			$table->tinyInteger("process_percentage")->unsigned()->nullable();
			$table->text("msg")->nullable();
			$table->boolean("error")->default(false);
			
			$table->index("source_file_id");
			
			$table->foreign("source_file_id", "source_file_id_frn")->references('id')->on('files')->onUpdate("restrict")->onDelete('restrict');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->dropForeign("source_file_id_frn");
			$table->dropColumn("source_file_id");
			$table->dropColumn("process_stage");
			$table->dropColumn("process_percentage");
			$table->dropColumn("msg");
			$table->dropColumn("error");
		});
	}

}
