<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<meta name="description" content="<?=e($description)?>">
		<meta name="author" content="">
		<link rel="shortcut icon" href="<?=asset("assets/img/favicon.ico");?>">
		<?= stylesheet_link_tag("includes/application") ?>
		<?= javascript_include_tag("includes/application") ?>
	</head>
	<body data-baseurl="<?=e($baseUrl)?>" data-assetsbaseurl="<?=e(asset(""))?>" data-csrftoken="<?=e($csrfToken)?>" data-loggedin="<?=$loggedIn?"1":"0"?>">
		@yield('body', '')
	</body>
</html>