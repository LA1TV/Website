<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<meta name="description" content="<?=e($description)?>">
		<meta name="author" content="">
		<link rel="shortcut icon" href="<?=asset("assets/img/favicon.ico");?>">
		<?php if (!$allowRobots): ?>
		<meta name="robots" content="noindex, nofollow">
		<?php endif; ?>
		<?= stylesheet_link_tag($stylesheetApplicationPath) ?>
		<?php //javascript_include_tag($javascriptApplicationPath) ?>
		<script data-main="<?=asset("scripts/main");?>" src="<?=asset("scripts/require.js");?>"></script>
	</head>
	<body data-pagedata="<?=e(json_encode($pageData));?>">
		@yield('body', '')
	</body>
</html>