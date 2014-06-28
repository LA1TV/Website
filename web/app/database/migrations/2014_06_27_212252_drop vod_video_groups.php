<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropVodVideoGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('vod_video_groups');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('vod_video_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('source_file_id')->unsigned();
			$table->tinyInteger('process_stage')->unsigned()->default(0);
			$table->timestamps();
			
			$table->index("source_file_id");
			
			$table->foreign("source_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
		});

	}

}
