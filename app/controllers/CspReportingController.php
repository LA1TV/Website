<?php namespace uk\co\la1tv\website\controllers;

use Response;

class CspReportingController extends BaseController {

	// collects csp reports sent from web browsers 
	public function report() {
		// not actually logging any reports at the moment
		// csp requires there to be a report endpoint though otherwise it causes an error in the browsers
		// so this is it
		return Response::make("", 204); // 204 = No Content
	}
}
