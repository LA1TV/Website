<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaItemsLikes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media_items_likes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('site_user_id')->unsigned();
			$table->integer('media_item_id')->unsigned();
			$table->boolean('is_like');
			$table->timestamps();
			
			$table->index("site_user_id");
			$table->index("media_item_id");
			
			$table->foreign("site_user_id")->references('id')->on('site_users')->onUpdate("restrict")->onDelete('cascade');
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
		Schema::drop('media_items_likes');
	}

}
