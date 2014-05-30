<?php

class AllowedFileTypesHelper {
	
	public static function getImages() {
		return array("jpg", "jpeg");
	}
	
	public static function getVideos() {
		return array("mp4");
	}
	
	public static function getAll() {
		return array_merge(self::getImages(), self::getVideos());
	}
}