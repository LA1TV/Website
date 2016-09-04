<?php

class LiveThumbnails {
	
	public static function startGenerator($streamUrl) {
		$result = self::makeRequest("v1/start", "POST", array(
			"url"	=> $streamUrl
		));
		if ($result["statusCode"] !== 200) {
			return null;
		}
		$publicBaseUri = Config::get("liveThumbnails.publicBaseUri");
		if (is_null($publicBaseUri)) {
			throw new Exception('Missing base url.');
		}
		$id = $result["data"]["id"];
		return array(
			"id" => $id,
			"manifestUri" => $publicBaseUri . "thumbnails-".$id.".json"
		);
	}

	public static function stopGenerator($id) {
		$result = self::makeRequest("v1/generators/".$id, "DELETE");
		return $result["statusCode"] === 200;
	}

	public static function checkStillRunning($id) {
		$result = self::makeRequest("v1/generators/".$id);
		return $result["statusCode"] === 200;
	}

	private static function makeRequest($url, $method="GET", $data=array(), $requestTimeout=10000) {
		$encodedData = http_build_query($data);
		
		$secret = Config::get("liveThumbnails.secret");
		if (is_null($secret)) {
			$secret = "";
		}

		$serviceBaseUri = Config::get("liveThumbnails.serviceBaseUri");
		if (is_null($serviceBaseUri)) {
			throw new Exception("Missing live thumbnails service base uri.");
		}

		$ch = curl_init($serviceBaseUri . $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $requestTimeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Length: ' . strlen($encodedData),
			'x-secret: ' . $secret
		));

		$result = curl_exec($ch);
		
		if (curl_errno($ch) > 0) {
			// curl error, possibly timeout
			curl_close($ch);
			return array("statusCode"=>null, "data"=>null);
		}
		
		$responseStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if ($responseStatusCode === 200) {
			$responseData = json_decode($result, true);
			return array("statusCode"=>$responseStatusCode, "data"=>$responseData);
		}
		else {
			return array("statusCode"=>$responseStatusCode, "data"=>null);
		}
	}

}