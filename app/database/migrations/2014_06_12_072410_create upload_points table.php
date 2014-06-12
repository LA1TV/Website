<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadPointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('upload_points', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("file_type_id")->unsigned();
			$table->text('description')->nullable();
			$table->timestamps();
			
			$table->index("file_type_id");
			$table->foreign("file_type_id")->references('id')->on('file_types')->onUpdate("restrict")->onDelete('restrict');	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('upload_points');
	}

}
