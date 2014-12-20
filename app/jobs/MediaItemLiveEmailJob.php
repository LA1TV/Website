<?php namespace uk\co\la1tv\website\jobs;

use DB;
use Carbon;
use EmailHelpers;
use Log;
use uk\co\la1tv\website\models\MediaItem;
use uk\co\la1tv\website\models\EmailTasksMediaItem;

class MediaItemLiveEmailsJob {
	
	// responsible for sending emails saying a mediaitem has gone live.
	// expects data to contain "mediaItemId" of the media item that has just gone live
	public function fire($job, $data) {	
	
		$mediaItemId = $data['mediaItemId'];
		Log::info("Starting job to send email for media item with id ".$mediaItemId." which has gone live.");
		
		// only attempt to run the job once
		if ($job->attempts() <= 1) {
			
			// retrieve the media item
			$mediaItem = DB::transaction(function() use (&$mediaItemId) {
				$mediaItem = MediaItem::accessible()->whereHas("liveStreamItem", function($q) {
					$q->accessible()->live();
				})->whereHas("emailTasksMediaItem", function($q2) {
					$q2->where("message_type_id", EmailHelpers::getMessageTypeIds()['liveNow'])->where("created_at", ">=", Carbon::now()->subMinutes(15));
				}, "=", 0)->where("id", $mediaItemId)->lockForUpdate()->first();
				
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
			
		}
		
		// remove the job from the queue
		Log::info("Finished job to send email for media item with id ".$mediaItemId." which has gone live.");
		$job->delete();
	}
	
}