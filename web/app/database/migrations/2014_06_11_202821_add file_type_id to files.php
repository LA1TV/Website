<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileTypeIdToFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('files', function(Blueprint $table)
		{
			$table->integer("file_type_id")->unsigned();
			
			$table->index("file_type_id");
			$table->foreign("file_type_id", "file_type_id_foreign")->references('id')->on('file_types')->onUpdate("restrict")->onDelete('restrict');	
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
			$table->dropForeign("file_type_id_foreign");
			$table->dropColumn("file_type_id");
		});
	}

}
