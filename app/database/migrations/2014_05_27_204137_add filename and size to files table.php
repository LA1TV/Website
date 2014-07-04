<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFilenameAndSizeToFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->string("filename", 50)->nullable();
			$table->bigInteger("size")->unsigned();
			$table->string("session_id", 255)->nullable();
			
			$table->index("session_id");
			
			$table->foreign("session_id")->references('id')->on('sessions')->onUpdate("restrict")->onDelete('set null');

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
			$table->dropColumn("filename");
			$table->dropColumn("size");
			$table->dropForeign('files_session_id_foreign');
			$table->dropColumn("session_id");
		});
	}

}
