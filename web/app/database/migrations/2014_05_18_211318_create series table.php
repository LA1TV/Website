<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('series', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 50);
			$table->string('description')->nullable();
			$table->integer('cover_file_id')->unsigned()->nullable();
			$table->integer('side_banner_file_id')->unsigned()->nullable();
			$table->boolean('enabled');
			$table->timestamp('scheduled_publish_time')->nullable();
			$table->timestamps();
			
			$table->index("cover_file_id");
			$table->index("side_banner_file_id");
			
			$table->foreign("cover_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
			$table->foreign("side_banner_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('series');
	}

}
