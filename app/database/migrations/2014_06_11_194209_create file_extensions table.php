<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileExtensionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('file_extensions', function(Blueprint $table)
		{
			$table->integer('id')->unsigned();
			$table->string("extension", 10);
			$table->timestamps();
			
			$table->primary('id');
			$table->unique('extension');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('file_extensions');
	}

}
