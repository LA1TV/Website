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
			$table->boolean('cover_file_id')->nullable()->index();
			$table->boolean('side_banner_file_id')->nullable()->index();
			$table->boolean('enabled');
			$table->timestamp('scheduled_publish_time')->nullable();
			$table->timestamps();
			
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
