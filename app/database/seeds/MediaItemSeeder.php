<?php

use uk\co\la1tv\website\models\SiteUser;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemComment;
use uk\co\la1tv\website\models\MediaItemLike;
use uk\co\la1tv\website\models\File;

class MediaItemSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		$mediaItemVideo = new MediaItemVideo(array(
			"is_live_recording"	=>	true,
			"time_recorded"	=>	 Carbon::now()->subHour(),
			"description"	=>	"Breakfast show description that should override general mediafile one.",
			"enabled"	=>	true
		));
		$mediaItem = new MediaItem(array(
			"name"	=>	"Breakfast Show!",
			"description"	=>	"This is the breakfast show description.",
			"enabled"	=>	true
		));
		$mediaItem->coverFile()->associate(File::find(1));
		$mediaItem->sideBannerFile()->associate(File::find(2));
		DB::transaction(function() use (&$mediaItem, &$mediaItemVideo) {
			$mediaItem->save();
			$mediaItem->videoItem()->save($mediaItemVideo);
		});
		$this->addLikes($mediaItem);
		$this->addComments($mediaItem);
		
		$mediaItemLiveStream = new MediaItemLiveStream(array(
			"enabled"	=>	true
		));
		$mediaItem = new MediaItem(array(
			"name"	=>	"Lunchtime Show!",
			"description"	=>	"This is the lunchtime show description.",
			"enabled"	=>	true
		));
		$mediaItem->coverFile()->associate(File::find(3));
		DB::transaction(function() use (&$mediaItem, &$mediaItemLiveStream) {
			$mediaItem->save();
			$mediaItem->liveStreamItem()->save($mediaItemLiveStream);
		});
		$this->addLikes($mediaItem);
		$this->addComments($mediaItem);
			
		$this->command->info('Media items created!');
	}
	
	private function addLikes($mediaItem) {
		
		$noUsers = SiteUser::count();
		if ($noUsers <= 0) {
			$this->command->info("Can't add likes. No users!");
			return;
		}
		
		$noToCreate = rand(0, 10);
		
		if ($noToCreate > 0) {
			$users = SiteUser::take($noToCreate)->get();
			
			for ($i=0; $i<$noToCreate; $i++) {
				$like = new MediaItemLike(array(
					"is_like"	=> rand(0, 1)
				));
				$user = $users[rand(0, count($users)-1)];
				
				$like->siteUser()->associate($user);
				$like->mediaItem()->associate($mediaItem);
				$like->save();
			}
		}
	}
	
	private function addComments($mediaItem) {
		
		$noUsers = SiteUser::count();
		if ($noUsers <= 0) {
			$this->command->info("Can't add comments. No users!");
			return;
		}
		
		$comments = array(
			"This is a random comment.",
			"Another completley different random commment.",
			"Blah blah blah blah blah blah blah blah blah blah something interesting.",
			"<script>alert('xss');</script> some <strong>xss</strong>"
		);
		
		$noToCreate = rand(0, 20);
		
		if ($noToCreate > 0) {
			$users = SiteUser::take($noToCreate)->get();
			
	
			for ($i=0; $i<$noToCreate; $i++) {
				$comment = new MediaItemComment(array(
					"msg"	=> $comments[rand(0, count($comments)-1)]
				));
				$user = $users[rand(0, count($users)-1)];
				
				$comment->siteUser()->associate($user);
				$comment->mediaItem()->associate($mediaItem);
				$comment->save();
			}
		}
	}

}
