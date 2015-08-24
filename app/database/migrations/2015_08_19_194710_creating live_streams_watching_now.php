<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingLiveStreamsWatchingNow extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('live_streams_watching_now', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("live_stream_id")->unsigned();
			$table->string("session_id", 255);
			$table->timestamps();
			
			$table->foreign("live_stream_id")->references('id')->on('live_streams')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("session_id")->references('id')->on('sessions')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('live_streams_watching_now');
	}

}
