<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVodDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vod_data', function(Blueprint $table)
		{
			$table->increments('id');
			$table->double("duration")->unsigned();
			$table->integer("file_id")->unsigned();
			$table->timestamps();

			$table->unique("file_id");
			$table->foreign("file_id", "vod_data_file_id_foreign")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vod_data');
	}

}
