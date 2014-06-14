<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFileIdToSourceFileId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->renameColumn('file_id', 'source_file_id');
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
			$table->renameColumn('source_file_id', 'file_id');
		});
	}

}
