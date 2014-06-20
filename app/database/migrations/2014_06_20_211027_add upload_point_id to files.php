<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUploadPointIdToFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->integer("upload_point_id")->unsigned();
			
			$table->index("upload_point_id");
			$table->foreign("upload_point_id", "upload_point_id_foreign")->references('id')->on('upload_points')->onUpdate("restrict")->onDelete('cascade');	

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
			$table->dropForeign("upload_point_id_foreign");
			$table->dropColumn("upload_point_id");
		});
	}

}
