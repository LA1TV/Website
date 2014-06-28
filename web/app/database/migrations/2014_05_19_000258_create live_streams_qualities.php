<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLiveStreamsQualities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('live_streams_qualities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("live_stream_id")->unsigned();
			$table->string("quality_id", 20);
			$table->tinyInteger("position")->unsigned()->unique();
			$table->timestamps();
			
			$table->index("live_stream_id");
			
			$table->foreign("live_stream_id")->references('id')->on('live_streams')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('live_streams_qualities');
	}

}
