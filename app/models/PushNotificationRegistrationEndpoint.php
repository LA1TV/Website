<?php namespace uk\co\la1tv\website\models;

class PushNotificationRegistrationEndpoint extends MyEloquent {

	protected $table = 'push_notification_registration_endpoints';
	protected $fillable = array('url', 'key', 'auth_secret');

}