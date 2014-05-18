<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionToGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permission_to_group', function(Blueprint $table)
		{
			$table->integer("group_id")->unsigned()->references('id')->on('permission_groups')->onUpdate->("restrict")->onDelete('cascade');
			$table->integer("permission_id")->unsigned()->references('id')->on('permissions')->onUpdate->("restrict")->onDelete('restrict');
			$table->integer("permission_flag")->unsigned();
			$table->primary(array("group_id", "permission_id"));
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
		Schema::drop('permission_to_group');
	}

}
