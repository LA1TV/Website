<?php

use uk\co\la1tv\website\models\SiteUser;

class EmailHelpers {
	
	private static $messageTypeIds = array(
		"liveInFifteen"	=> 1, // show live in 15 minutes
		"liveNow"		=> 2  // show live/vod available now
	);
	
	public static function getMessageTypeIds() {
		return self::$messageTypeIds;
	}
	
	public static function sendMediaItemEmail($mediaItem, $heading, $msg) {
		
		$data = self::getMediaItemEmailData($mediaItem, $heading, $msg);
	
		// get all users that have emails enabled
		$users = SiteUser::whereNotNull("fb_email")->where("email_notifications_enabled", true)->get();
		foreach($users as $user) {
			
			// attempt to update the users facebook info
			Facebook::updateUserOpenGraph($user);
			
			$email = $user->fb_email;
			// check the email hasn't become null after the facebook update and that we have permission from facebook to use the email
			// also check the last time the users details were updated successfully and if it is longer than a month ago then presume it's stale and the facebook token has expired and the user hasn't renewed it by logging back in for a long time
			$cutOffTime = with(Carbon::now())->subMonths(1);
			if ($user->hasFacebookPermission("email") && !is_null($email) && $user->fb_last_update_time->timestamp > $cutOffTime->timestamp) {
				$this->info("Sending email to user with id ".$user->id." and email \"".$email."\".");
				// send the email
				Mail::send('emails.mediaItem', $data, function($message) use (&$email) {
					$message->to($email)->subject($data['subject']);
				});
			}
		}
	}
	
	private static function getMediaItemEmailData($mediaItem, $subject, $heading, $msg) {
		$playlist = $mediaItem->getDefaultPlaylist();
		$coverResolution = Config::get("imageResolutions.coverArt")['email'];
		$mediaItemTitle = $playlist->generateEpisodeTitle($mediaItem);
		return array(
			"subject"				=> str_replace("{title}", $mediaItemTitle, $subject),
			"heading"				=> $heading,
			"msg"					=> $msg,
			"coverImgWidth"			=> $coverResolution['w'],
			"coverImgHeight"		=> $coverResolution['h'],
			"coverImgUri"			=> $playlist->getMediaItemCoverArtUri($mediaItem, $coverResolution['w'], $coverResolution['h']),
			"mediaItemTitle"		=> $mediaItemTitle,
			"mediaItemDescription"	=> $mediaItem->description,
			"mediaItemUri"			=> $playlist->getMediaItemUri($mediaItem),
			"facebookUri"			=> Config::get("socialMediaUris.facebook"),
			"twitterUri"			=> Config::get("socialMediaUris.twitter"),
			"contactEmail"			=> Config::get("contactEmails.general"),
			"developmentEmail"		=> Config::get("contactEmails.development"),
			"accountSettingsUri"	=> URL::route('account')
		);
	}
}