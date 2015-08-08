<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('production_roles', function(Blueprint $table)
		{
			$table->integer('id')->unsigned();
			$table->text("name");
			$table->text("description")->nullable();
			$table->smallInteger("position")->unsigned();
			$table->timestamps();
			
			$table->primary('id');
			$table->unique("position");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('production_roles');
	}

}
