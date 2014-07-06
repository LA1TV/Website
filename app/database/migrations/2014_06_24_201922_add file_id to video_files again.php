<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileIdToVideoFilesAgain extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_files', function(Blueprint $table)
		{
			$table->integer('file_id')->unsigned();
			
			$table->index("file_id");
			
			$table->foreign("file_id", "file_id_foreign")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');

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
			$table->dropForeign("file_id_foreign");
			$table->dropColumn("file_id");
		});
	}

}
