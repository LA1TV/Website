<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtensionToFileType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('file_extension_to_file_type', function(Blueprint $table)
		{
			$table->integer("file_extension_id")->unsigned();
			$table->integer("file_type_id")->unsigned();
			$table->primary(array("file_extension_id", "file_type_id"), "primary_key");
			
			$table->foreign("file_extension_id", "file_extension_foreign")->references('id')->on('file_extensions')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("file_type_id", "file_type_foreign")->references('id')->on('file_types')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('file_extension_to_file_type');
	}

}
