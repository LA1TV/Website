<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserToGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_to_group', function(Blueprint $table)
		{
			$table->integer("user_id")->unsigned();
			$table->integer("group_id")->unsigned();
			$table->primary(array("user_id", "group_id"));
			$table->timestamps();
			
			$table->foreign("user_id")->references('id')->on('users')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("group_id")->references('id')->on('permission_groups')->onUpdate("restrict")->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_to_group');
	}

}
