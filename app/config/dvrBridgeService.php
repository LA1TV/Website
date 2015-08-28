<?php

$encryptionKeyMd5 = isset($_ENV['ENCRYPTION_KEY']) ? md5($_ENV['ENCRYPTION_KEY']) : null;
$prefix = null;
if (!is_null($encryptionKeyMd5)) {
	$prefix = $encryptionKeyMd5;
}
else {
	$prefix = "local:".md5(php_uname());
}

return array(
	// this will be prepended to the stream ids that are used with the DVR Bridge Service
	// this means multiple servers can use the same service (with same api key) without conflicts
	// providing these prefix's are different
	// uses a hash of the encryption key that has been configured
	// if there isn't one configured (eg local environment) then the machine name will be used instead
	"idPrefix" => $prefix . ":",
);
