<?php

use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemComment;
use uk\co\la1tv\website\models\MediaItemLike;

class MediaItemSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		DB::transaction(function() {
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			MediaItem::truncate();
			MediaItemLiveStream::truncate();
			MediaItemVideo::truncate();
			MediaItemComment::truncate();
			MediaItemLike::truncate();
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
		
			$mediaItemLiveStream = new MediaItemLiveStream(array(
				"enabled"	=>	true
			));
			
			$mediaItem = new MediaItem(array(
				"title"	=>	"Lunchtime Show!",
				"description"	=>	"This is the lunchtime show description.",
				"enabled"	=>	true
			));
			$mediaItem->save();
			$mediaItem->liveStreamItem()->save($mediaItemLiveStream);
		});
			
		$this->command->info('Media items created!');
	}

}
