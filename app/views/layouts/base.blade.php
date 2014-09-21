<!DOCTYPE html>
<html lang="en">
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<meta name="description" content="<?=e($description)?>">
		<meta name="author" content="">
		<?php if (isset($openGraphProperties)): ?>
		<?php foreach($openGraphProperties as $a): ?>
		<meta name="<?=e($a['name']);?>" content="<?=e($a['content']);?>">
		<?php endforeach; ?>
		<?php endif; ?>
		<link rel="shortcut icon" href="<?=asset("assets/img/favicon.ico");?>">
		<?php if (!$allowRobots): ?>
		<meta name="robots" content="noindex, nofollow">
		<?php endif; ?>
		<link href="<?=e($cssBootstrap);?>" rel="stylesheet" type="text/css">
		<script data-main="<?=e($requireJsBootstrap);?>" src="<?=asset("assets/scripts/require.js");?>"></script>
	</head>
	<body data-pagedata="<?=e(json_encode($pageData));?>">
		@yield('body', '')
	</body>
</html>