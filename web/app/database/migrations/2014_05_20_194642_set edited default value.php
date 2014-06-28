<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetEditedDefaultValue extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_comments', function(Blueprint $table)
		{
			$table->dropColumn("edited");
		});
		Schema::table('media_items_comments', function(Blueprint $table)
		{
			$table->boolean('edited')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items_comments', function(Blueprint $table)
		{
			$table->dropColumn("edited");
		});
		Schema::table('media_items_comments', function(Blueprint $table)
		{
			$table->boolean('edited');
		});
	}

}
