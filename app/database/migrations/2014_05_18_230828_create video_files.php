<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoFiles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('video_id')->unsigned()->nullable();
			$table->integer('width')->unsigned();
			$table->integer('height')->unsigned();
			$table->tinyInteger('encode_stage')->unsigned()->default(0);
			$table->timestamps();
			
			$table->index("video_id");
			
			$table->foreign("video_id")->references('id')->on('videos')->onUpdate("restrict")->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_files');
	}

}
