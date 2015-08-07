<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldFileIds extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('old_file_ids', function(Blueprint $table)
		{
			$table->integer('old_file_id')->unsigned();
			$table->integer('new_file_id')->unsigned();
			
			$table->foreign("new_file_id")->references('id')->on('files')->onUpdate("restrict")->onDelete('cascade');
			$table->primary(array('old_file_id', 'new_file_id'));
			$table->unique(array('old_file_id'));
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('old_file_ids');
	}

}
