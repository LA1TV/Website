<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('fb_uid')->unsigned();
			$table->text('first_name');
			$table->text('last_name');
			$table->text('name');
			$table->text('email');
			$table->timestamp('last_seen');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_users');
	}

}
