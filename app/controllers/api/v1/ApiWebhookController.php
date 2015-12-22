<?php namespace uk\co\la1tv\website\controllers\api\v1;

use uk\co\la1tv\website\controllers\api\ApiBaseController;
use ApiAuth;
use Event;
use FormHelpers;

class ApiWebhookController extends ApiBaseController {

	public function postConfigure() {
		ApiAuth::hasUserOrApiException();
		$url = FormHelpers::getValue("url");
		if (is_null($url)) {
			return $this->respondServerError("You have not provided the URL in a 'url' key.");
		}
		else if ($url !== "") {
			if (strlen($url) > 500) {
				return $this->respondServerError("The URL must be less than 500 characters.");
			}
			else if (filter_var($url, FILTER_VALIDATE_URL) === false) {
				return $this->respondServerError("The URL is invalid.");
			}
		}
		if ($url === "") {
			$url = null;
		}
		$apiUser = ApiAuth::getUser();
		$apiUser->webhook_url = $url;
		if (!$apiUser->save()) {
			return $this->respondServerError();
		}
		return $this->respond(array("url"=>$url));
	}

	public function postTest() {
		ApiAuth::hasUserOrApiException();
		$apiUser = ApiAuth::getUser();
		if (!$apiUser->canUseWebhooks()) {
			return $this->respondServerError("You do not have permisson to use webhooks.");
		}
		$this->log("Request for webhook test.");
		$response = null;
		if (is_null($apiUser->webhook_url)) {
			$response = array(
				"success" => false,
				"reason" => "You have not provided a URL to send the POST request to."
			);
		}
		else {
			Event::fire('apiWebhookTest', array($apiUser));
			$response = array("success"=>true);
		}
		return $this->respond($response);
	}
}
