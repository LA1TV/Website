<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomUrisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('custom_uris', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string("name", 50)->unique();
			$table->integer("uriable_id")->unsigned();
			$table->text("uriable_type");
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
		Schema::drop('custom_uris');
	}

}
