<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTasksMediaItem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_tasks_media_item', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("media_item_id")->unsigned();
			$table->integer("message_type_id")->unsigned();
			$table->timestamps();
			
			$table->foreign("media_item_id", "email_tasks_media_item_media_item_id_foreign")->references('id')->on('media_items')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_tasks_media_item');
	}

}
