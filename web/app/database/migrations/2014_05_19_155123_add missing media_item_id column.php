<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingMediaItemIdColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('media_items_comments', function(Blueprint $table)
		{
			$table->integer('media_item_id')->unsigned();
			
			$table->index("media_item_id");
			
			$table->foreign("media_item_id")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('cascade');
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
			$table->dropForeign('media_items_comments_media_item_id_foreign');
			$table->dropColumn("media_item_id");
		});
	}

}
