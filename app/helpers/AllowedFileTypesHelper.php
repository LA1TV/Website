<?php

class AllowedFileTypesHelper {
	
	public static function getImages() {
		return array("jpg", "jpeg");
	}
	
	public static function getVideos() {
		return array("mp4");
	}
}