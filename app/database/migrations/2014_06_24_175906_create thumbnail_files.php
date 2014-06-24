<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThumbnailFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('thumbnail_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('source_file_id')->unsigned();
			$table->integer('file_id')->unsigned();
			$table->tinyInteger('process_stage')->unsigned()->default(0);
			$table->boolean("error")->default(false);
			$table->timestamps();
			
			$table->index("source_file_id");
			$table->index("file_id");
			
			$table->foreign("source_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('restrict');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('thumbnail_files');
	}

}
