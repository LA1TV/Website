<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoFilesAgain extends Migration {

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
			$table->integer('media_items_video_id')->unsigned();
			$table->smallInteger('width')->unsigned();
			$table->smallInteger('height')->unsigned();
			$table->tinyInteger('encode_stage')->unsigned()->default(0);
			$table->timestamps();
			
			$table->index("media_items_video_id");
			
			$table->foreign("media_items_video_id")->references('id')->on('media_items_video')->onUpdate("restrict")->onDelete('cascade');

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
