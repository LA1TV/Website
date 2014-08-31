<?php

use uk\co\la1tv\website\models\LiveStream;
use uk\co\la1tv\website\models\LiveStreamQuality;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemComment;
use uk\co\la1tv\website\models\MediaItemLike;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\Permission;
use uk\co\la1tv\website\models\PermissionGroup;
use uk\co\la1tv\website\models\Playlist;
use uk\co\la1tv\website\models\Show;
use uk\co\la1tv\website\models\SiteUser;
use uk\co\la1tv\website\models\User;
use uk\co\la1tv\website\models\VideoFile;
use uk\co\la1tv\website\models\QualityDefinition;

class TruncateTablesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
			
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		LiveStream::truncate();
		MediaItem::truncate();
		MediaItemComment::truncate();
		MediaItemLike::truncate();
		MediaItemLiveStream::truncate();
		MediaItemVideo::truncate();
		Permission::truncate();
		PermissionGroup::truncate();
		QualityDefinition::truncate();
		LiveStreamQuality::truncate();
		Playlist::truncate();
		Show::truncate();
		SiteUser::truncate();
		User::truncate();
		VideoFile::truncate();
		DB::table("media_item_to_playlist")->truncate();
		DB::table("permission_to_group")->truncate();
		DB::table("user_to_group")->truncate();
		DB::table("live_stream_qualitiy_to_live_stream")->truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
		
		$this->command->info('Tables truncated!');
	}
}
