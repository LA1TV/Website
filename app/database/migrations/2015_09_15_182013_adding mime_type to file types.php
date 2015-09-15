<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingMimeTypeToFileTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('file_types', function(Blueprint $table)
		{
			$table->string("mime_type", 100)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('file_types', function(Blueprint $table)
		{
			$table->dropColumn("mime_type");
		});
	}

}
