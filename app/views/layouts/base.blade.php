<!DOCTYPE html>
<!-- Version: <?=e($version);?> -->
<html lang="en">
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<?php if (isset($description)): ?>
		<meta name="description" content="<?=e($description)?>">
		<?php endif; ?>
		<meta name="author" content="">
		<?php if (isset($openGraphProperties)): ?>
		<?php foreach($openGraphProperties as $a): ?>
		<meta property="<?=e($a['name']);?>" content="<?=e($a['content']);?>">
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (isset($twitterProperties)): ?>
		<?php foreach($twitterProperties as $a): ?>
		<meta name="twitter:<?=e($a['name']);?>" content="<?=e($a['content']);?>">
		<?php endforeach; ?>
		<?php endif; ?>
		<link rel="shortcut icon" href="<?=asset("assets/img/favicon.ico");?>">
		<?php if (!$allowRobots): ?>
		<meta name="robots" content="noindex, nofollow">
		<?php endif; ?>
		<?php if (isset($cssFiles)): ?>
		<?php foreach($cssFiles as $a): ?>
		<link href="<?=e($a);?>" rel="stylesheet" type="text/css">
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (isset($cssBootstrap)): ?>
		<link href="<?=e($cssBootstrap);?>" rel="stylesheet" type="text/css">
		<?php endif; ?>
		<?php if (isset($jsFiles)): ?>
		<?php foreach($jsFiles as $a): ?>
		<script src="<?=e($a);?>"></script>
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (isset($requireJsBootstrap)): ?>
		<script data-main="<?=e($requireJsBootstrap);?>" src="<?=asset("assets/scripts/require.js");?>"></script>
		<?php endif; ?>
	</head>
	<body data-pagedata="<?=e(json_encode(isset($pageData) ? $pageData : array()));?>">
		@yield('body', '')
	</body>
</html>