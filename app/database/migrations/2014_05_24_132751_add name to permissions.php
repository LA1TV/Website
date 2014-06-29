<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameToPermissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('permissions', function(Blueprint $table)
		{
			$table->string("name", 50);
			$table->dropColumn("description");
		});
		Schema::table('permissions', function(Blueprint $table)
		{
			$table->text("description")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('permissions', function(Blueprint $table)
		{
			$table->dropColumn("name");
			$table->dropColumn("description");
		});
		Schema::table('permissions', function(Blueprint $table)
		{
			$table->text("description");
		});
	}

}
