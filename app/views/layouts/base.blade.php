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
		<?= javascript_include_tag($javascriptApplicationPath) ?>
	</head>
<?php
	$bodyDataString = "";
	foreach($pageData as $key=>$val) {
		$bodyDataString .= ' data-'.$key.'="'.e($val).'"';
	};
?>
	<body<?=$bodyDataString?>>
<?php
	unset($bodyDataString);
?>
		@yield('body', '')
	</body>
</html>