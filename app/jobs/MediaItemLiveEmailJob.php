<?php namespace uk\co\la1tv\website\jobs;

use DB;
use Carbon;
use EmailHelpers;
use DebugHelpers;
use Log;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\EmailTasksMediaItem;

class MediaItemLiveEmailsJob {
	
	// responsible for sending emails saying a mediaitem has gone live.
	// expects data to contain "mediaItemId" of the media item that has just gone live
	public function fire($job, $data) {	
	
		if (!DebugHelpers::shouldSiteBeLive()) {
			$job->release(); // put the job back on the queue
			return;
		}
		
		// remove the job from the queue to make sure it only runs once even if an exception is thrown
		$job->delete();
	
		$mediaItemId = $data['mediaItemId'];
		Log::info("Starting job to send email for media item with id ".$mediaItemId." which has gone live.");
		
		// retrieve the media item
		$mediaItem = DB::transaction(function() use (&$mediaItemId) {
			$mediaItem = MediaItem::accessible()->whereHas("liveStreamItem", function($q) {
				$q->accessible()->live();
			})->whereHas("emailTasksMediaItem", function($q2) {
				$q2->where("message_type_id", EmailHelpers::getMessageTypeIds()['liveNow'])->where("created_at", ">=", Carbon::now()->subMinutes(15));
			}, "=", 0)->where("email_notifications_enabled", true)->where("id", $mediaItemId)->lockForUpdate()->first();
			
			if (!is_null($mediaItem)) {
				$emailTask = new EmailTasksMediaItem(array(
					"message_type_id"	=> EmailHelpers::getMessageTypeIds()['liveNow']
				));
				// create an entry in the tasks table for the emails that are going to be sent
				$mediaItem->emailTasksMediaItem()->save($emailTask);
			}
			
			return $mediaItem;
		});
		
		if (!is_null($mediaItem)) {
			EmailHelpers::sendMediaItemEmail($mediaItem, 'LA1:TV Live Now With "{title}"', "Live now!", "We are streaming live right now!");
		}
		Log::info("Finished job to send email for media item with id ".$mediaItemId." which has gone live.");
	}
	
}