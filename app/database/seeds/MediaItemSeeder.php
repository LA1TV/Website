<?php

use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemLiveStream;

class MediaItemSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		
//		$mediaItemLiveStream = new MediaItemLiveStream();
//		$mediaItemLiveStream->enabled = true;
//		$mediaItemLiveStream->save();
//		MediaItem::find(1)->liveStreamItem()->save($mediaItemLiveStream);
//		$mediaItem->save();
//		return;
		
		$mediaItemLiveStream = new MediaItemLiveStream();
		$mediaItemLiveStream->enabled = true;
		
		$mediaItem = new MediaItem();
		$mediaItem->name = "Lunchtime Show!";
		$mediaItem->description = "This is the lunchtime show description.";
		$mediaItem->enabled = true;
		$mediaItem->liveStreamItem()->save($mediaItemLiveStream);
		
		$mediaItem->push();
		
		$this->command->info('Media items created!');
	}

}
