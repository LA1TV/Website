<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('image_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->smallInteger("width")->unsigned();
			$table->smallInteger("height")->unsigned();
			$table->integer("file_id")->unsigned();
			$table->timestamps();
			
			$table->index("file_id");
			
			$table->foreign("file_id", "file_id_frn")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('image_files');
	}

}
