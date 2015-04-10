<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangingFbUidFromBigintToVarcharAsPerFacebooksSpecification extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->renameColumn('fb_uid', 'fb_uid_tmp');
			$table->dropIndex("site_users_fb_uid_unique");
		});
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->string("fb_uid", 255);
		});
		$this->transfer("fb_uid_tmp", "fb_uid");
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->unique("fb_uid");
			$table->dropColumn("fb_uid_tmp");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->renameColumn('fb_uid', 'fb_uid_tmp');
			$table->dropIndex("site_users_fb_uid_unique");
		});
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->bigInteger("fb_uid");
		});
		$this->transfer("fb_uid_tmp", "fb_uid");
		Schema::table('site_users', function(Blueprint $table)
		{
			$table->unique("fb_uid");
			$table->dropColumn("fb_uid_tmp");
		});
	}
	
	private function transfer($from, $to) {
		$rows = DB::table('site_users')->get();
		foreach($rows as $row) {
			DB::table('site_users')->where('id', $row->id)->update([$to => $row->$from]);
		}
	}

}
