<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('file_types', function(Blueprint $table)
		{
			$table->integer("id")->unsigned();
			$table->text("description")->nullable();
			$table->timestamps();
			
			$table->primary("id");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('file_types');
	}

}
