<?php

use Symfony\Component\HttpFoundation\Request;

class MySessionMiddleware extends Illuminate\Session\Middleware {

	// override so that the session id will be retrieved from a "X-Session-Id" header if can't find it in a cookie
	
	// The reason for doing this is that for the embeddable player in the iframe the cookies are treated as "third party".
	// This means in safari at the moment if the user hasn't visited the site before to get a cookie set from there, safari
	// will block the creation of any cookies meaning the session cookie will not be contained in any ajax requests.
	// Doing this means that any ajax requests can set the session id in a header and keep the session alive for the entire
	// time the page is open, as the session id can just be stored in a variable in javascript.
	
	// The way safari appears to work at the moment is that it blocks all cookies, unless a cookie has been created for the same
	// domain but not in an iframe. Once a single cookie has been created at the domain then it seems to be able to update and
	// create others without issues from within the iframe.
	public function getSession(Request $request)
	{
		$session = $this->manager->driver();
		
		$id = $request->cookies->get($session->getName());
		
		if (is_null($id)) {
			// cookie is missing
			// check and see if it's in a "X-Session-Id" header and if it is use that.
			$id = $request->header("X-Session-Id");
		}
		
		$session->setId($id);

		return $session;
	}
	
}