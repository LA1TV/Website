<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingServerIdToFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->integer("server_id")->unsigned()->nullable();
			$table->index("server_id");
			$table->foreign("server_id", "files_server_id_foreign")->references('id')->on('processing_servers')->onUpdate("restrict")->onDelete('set null');
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
			$table->dropForeign("files_server_id_foreign");
			$table->dropColumn("server_id");
		});
	}

}
