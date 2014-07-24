<?php

use uk\co\la1tv\website\models\SiteUser;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\MediaItemLiveStream;
use uk\co\la1tv\website\models\MediaItemVideo;
use uk\co\la1tv\website\models\MediaItemComment;
use uk\co\la1tv\website\models\MediaItemLike;
use uk\co\la1tv\website\models\File;
use uk\co\la1tv\website\models\LiveStream;

class MediaItemSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		$videoItems = array(
			array("Breakfast Show!", "This is the breakfast show description."),
			array("BBC News"),
			array("BBC News 24"),
			array("Dragons Den"),
			array("Mock The Week!"),
			array("Some Other Show"),
			array("Soundbooth Sessions"),
			array("The LA1 Show"),
			array("The One Show"),
			array("Star Wars"),
			array("Sugar TV!"),
			array("The Incredibles"),
			array("University Challenge"),
			array("Countdown"),
			array("8 out of 10 Cats Does Countdown"),
			array("Jurassic Park"),
			array("Jurassic Park 2"),
			array("Shrek"),
			array("Shrek 2"),
			array("Shrek 3"),
			array("Mission Impossible")
		);
	
		foreach($videoItems as $a) {
			$mediaItemVideo = new MediaItemVideo(array(
				"is_live_recording"	=>	rand(0, 1) ? true : false,
				"time_recorded"	=>	 Carbon::now()->subHour(),
				"description"	=>	rand(0, 4) === 0 ? "A description that should override the general media item one." : null,
				"enabled"	=>	rand(0, 1) ? true : false
			));
			$mediaItem = new MediaItem(array(
				"name"	=>	$a[0],
				"description"	=>	count($a) >= 2 ? $a[1] : null,
				"enabled"	=>	rand(0, 1) ? true : false,
			));
			DB::transaction(function() use (&$mediaItem, &$mediaItemVideo) {
				$mediaItem->save();
				$mediaItem->videoItem()->save($mediaItemVideo);
			});
			$this->addLikes($mediaItem);
			$this->addComments($mediaItem);
		}
		
		$mediaItemLiveStream = new MediaItemLiveStream(array(
			"enabled"	=>	true
		));
		$mediaItemLiveStream->liveStream()->associate(LiveStream::find(1));
		$mediaItem = new MediaItem(array(
			"name"	=>	"Lunchtime Show!",
			"description"	=>	"This is the lunchtime show description.",
			"enabled"	=>	true
		));
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
