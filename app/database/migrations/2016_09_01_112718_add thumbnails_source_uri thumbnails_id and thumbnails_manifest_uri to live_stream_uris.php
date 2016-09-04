<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThumbnailsSourceUriThumbnailsIdAndThumbnailsManifestUriToLiveStreamUris extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->text("thumbnails_source_uri")->nullable();
			$table->string("thumbnails_id", 40)->nullable();
			$table->text("thumbnails_manifest_uri")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('live_stream_uris', function(Blueprint $table)
		{
			$table->dropColumn("thumbnails_source_uri");
			$table->dropColumn("thumbnails_id");
			$table->dropColumn("thumbnails_manifest_uri");
		});
	}

}
