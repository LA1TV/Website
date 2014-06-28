<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaItemsComments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media_items_comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('site_user_id')->unsigned()->nullable();
			$table->string('msg', 500);
			$table->boolean('edited', false);
			$table->timestamps();
			
			$table->index("site_user_id");
			
			$table->foreign("site_user_id")->references('id')->on('site_users')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('media_items_comments');
	}

}
