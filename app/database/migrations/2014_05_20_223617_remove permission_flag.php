<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePermissionFlag extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('permission_to_group', function(Blueprint $table)
		{
			$table->dropColumn('permission_flag');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('permission_to_group', function(Blueprint $table)
		{
			$table->tinyInteger('permission_flag')->unsigned();
		});
	}

}
