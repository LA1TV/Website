<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceFileFieldToMediaItemsVideo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->integer("source_file_id")->unsigned()->nullable();
		
			$table->index("source_file_id");
			
			$table->foreign("source_file_id", "source_file_foreign")->references('id')->on('files')->onUpdate("restrict")->onDelete('set null');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('media_items_video', function(Blueprint $table)
		{
			$table->dropForeign('source_file_foreign');
			$table->dropColumn("source_file_id");
		});
	}

}
