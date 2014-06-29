<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVodVideoGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vod_video_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('source_file_id')->unsigned();
			$table->tinyInteger('encode_stage')->unsigned()->default(0);
			$table->timestamps();
			
			$table->index("source_file_id");
			
			$table->foreign("source_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vod_video_groups');
	}

}
