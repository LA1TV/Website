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
			$table->integer("group_id");
			$table->integer("permission_id");
			$table->integer("permission_flag");
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
