<?php namespace uk\co\la1tv\website\controllers\home\account;

use uk\co\la1tv\website\controllers\home\HomeBaseController;
use View;
use URLHelpers;
use Facebook;
use App;
use Response;

class AccountController extends HomeBaseController {

	public function getIndex() {
		
		$loggedIn = Facebook::isLoggedIn();
		
		if ($loggedIn) {
			$emailNotificationButtonsData = array(
				array(
					"id"	=> 1,
					"text"	=> "Enabled"
				),
				array(
					"id"	=> 0,
					"text"	=> "Disabled"
				)
			);
			
			$user = Facebook::getUser();
			$emailNotificationsButtonsInitialId = $user->email_notifications_enabled ? "1" : "0";
		
			// force a request to happen to get the lastest permissions.
			Facebook::updateUserOpengraph($user);
			// save the model which may have just been updated
			$user->save();
			$haveEmailPermission = $user->hasFacebookPermission("email");
		}
		
		$view = View::make("home.account.index");
		$view->loggedIn = $loggedIn;
		if ($loggedIn) {
			$view->emailNotificationsButtonsData = $emailNotificationButtonsData;
			$view->emailNotificationsButtonsInitialId = $emailNotificationsButtonsInitialId;
			$view->haveEmailPermission = $haveEmailPermission;
			$view->logoutUri = URLHelpers::generateLogoutUrl();
		}
		$this->setContent($view, "account", "account", array(), "Account Settings");
	}
	
	public function postSetEmailNotificationsState() {
		if (!Facebook::isLoggedIn()) {
			App::abort(403);
		}
		
		$data = array("success" => false);
		
		if (isset($_POST['state_id'])) {
			$stateId = intval($_POST['state_id']);
			if ($stateId >= 0 && $stateId <= 1) {
				$user = Facebook::getUser();
				$user->email_notifications_enabled = $stateId === 1;
				$data['success'] = $user->save();
			}
		}
		return Response::json($data);
	}
}
