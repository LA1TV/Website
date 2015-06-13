<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatingProductionRolesMediaItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('production_roles_media_item', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("production_role_id")->unsigned();
			$table->text("name_override")->nullable();
			$table->text("description_override")->nullable();
			$table->timestamps();
			
			$table->foreign("production_role_id")->references('id')->on('production_roles')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('production_roles_media_item');
	}

}
