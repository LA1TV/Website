<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveProcessStageAndRenameErrorToProcessState extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->dropColumn("process_stage");
			$table->dropColumn("error");
			$table->tinyInteger("process_state")->unsigned()->default(0); // 0=processing 1=processed, 2=failed

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
			$table->tinyInteger("process_stage")->unsigned()->default(0);
			$table->boolean("error")->default(false);
			$table->dropColumn("process_state");
		});
	}

}
