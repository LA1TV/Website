<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('credits', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer("production_role_id")->unsigned();
			$table->integer("creditable_id")->unsigned();
			$table->text("creditable_type");
			$table->integer("site_user_id")->unsigned()->nullable();
			$table->text("name_override", 50)->nullable();
			$table->timestamps();
			
			$table->foreign("production_role_id", "credits_production_role_id_foreign")->references('id')->on('production_roles')->onUpdate("restrict")->onDelete('restrict');
			$table->foreign("site_user_id", "credits_site_user_id_foreign")->references('id')->on('site_users')->onUpdate("restrict")->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('credits');
	}

}
