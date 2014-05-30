<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileIdToVideoFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->integer("file_id")->unsigned()->nullable();
			
			$table->index("file_id");
			
			$table->foreign("file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
			
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
			$table->dropForeign('video_files_file_id_foreign');
			$table->dropColumn("file_id");
		});
	}

}
