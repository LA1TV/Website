<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedItemToMediaItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('related_item_to_media_item', function(Blueprint $table) {
		
			$table->integer("media_item_id")->unsigned();
			$table->integer("related_media_item_id")->unsigned();
			$table->tinyInteger("position");
			$table->primary(array("media_item_id", "related_media_item_id"), "primary_key");
			
			$table->foreign("media_item_id", "media_item_id_foreign")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('cascade');
			$table->foreign("related_media_item_id", "related_media_item_foreign")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('related_item_to_media_item');
	}

}
