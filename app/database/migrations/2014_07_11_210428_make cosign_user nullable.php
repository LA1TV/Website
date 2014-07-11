<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCosignUserNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn("cosign_user");
		});
		Schema::table('users', function(Blueprint $table)
		{
			$table->string('cosign_user', 32)->nullable()->unique();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn("cosign_user");
		});
		Schema::table('users', function(Blueprint $table)
		{
			$table->string('cosign_user', 32)->unique();
		});
	}

}
