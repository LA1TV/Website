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
			$table->integer('media_item_id')->unsigned()->nullable();
			$table->integer('width')->unsigned();
			$table->integer('height')->unsigned();
			$table->tinyInteger('encode_stage')->unsigned()->default(0);
			$table->timestamps();
			
			$table->index("media_item_id");
			
			$table->foreign("media_item_id")->references('id')->on('videos')->onUpdate("restrict")->onDelete('set null');
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
