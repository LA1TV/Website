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
			$table->dropForeign('video_files_file_id_foreign');
			$table->dropIndex('video_files_file_id_index');
		});
		
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->renameColumn('file_id', 'source_file_id');
		});
		
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->index("source_file_id");
			
			$table->foreign("source_file_id", "source_file_fk")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
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
			$table->dropForeign('source_file_fk');
			$table->dropIndex('video_files_source_file_id_index');
		});
		
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->renameColumn('source_file_id', 'file_id');
		});
		
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->index("file_id");
			
			$table->foreign("file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
		});
	}

}
