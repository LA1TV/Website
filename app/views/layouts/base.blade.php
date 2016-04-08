<!DOCTYPE html>
<!-- Version: <?=e(isset($version) ? $version : "[Unknown]");?> -->
<html lang="en">
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="mobile-web-app-capable" content="yes">
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
		<?php if (isset($manifestUri)): ?>
		<link rel="manifest" href="<?=e($manifestUri);?>">
		<?php endif; ?>
		<meta name="twitter:widgets:csp" content="on">
		<link rel="apple-touch-icon" sizes="57x57" href="<?=asset("assets/favicon/apple-touch-icon-57x57.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="60x60" href="<?=asset("assets/favicon/apple-touch-icon-60x60.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="72x72" href="<?=asset("assets/favicon/apple-touch-icon-72x72.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="76x76" href="<?=asset("assets/favicon/apple-touch-icon-76x76.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="114x114" href="<?=asset("assets/favicon/apple-touch-icon-114x114.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="120x120" href="<?=asset("assets/favicon/apple-touch-icon-120x120.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="144x144" href="<?=asset("assets/favicon/apple-touch-icon-144x144.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="152x152" href="<?=asset("assets/favicon/apple-touch-icon-152x152.png");?>?v=2">
		<link rel="apple-touch-icon" sizes="180x180" href="<?=asset("assets/favicon/apple-touch-icon-180x180.png");?>?v=2">
		<link rel="icon" type="image/png" href="<?=asset("assets/favicon/favicon-32x32.png");?>?v=2" sizes="32x32">
		<link rel="icon" type="image/png" href="<?=asset("assets/favicon/android-chrome-192x192.png");?>?v=2" sizes="192x192">
		<link rel="icon" type="image/png" href="<?=asset("assets/favicon/favicon-96x96.png");?>?v=2" sizes="96x96">
		<link rel="icon" type="image/png" href="<?=asset("assets/favicon/favicon-16x16.png");?>?v=2" sizes="16x16">
		<link rel="manifest" href="<?=asset("assets/favicon/manifest.json");?>?v=2">
		<link rel="shortcut icon" href="<?=asset("assets/favicon/favicon.ico");?>?v=2">
		<link rel="mask-icon" href="<?=asset("assets/favicon/favicon.svg");?>" color="#FF0000">
		<meta name="msapplication-TileColor" content="#603cba">
		<meta name="msapplication-TileImage" content="<?=asset("assets/favicon/mstile-144x144.png");?>?v=2">
		<meta name="theme-color" content="#ffffff">
		<?php if (!$allowRobots): ?>
		<meta name="robots" content="noindex, nofollow">
		<?php endif; ?>
		<script type="text/javascript">
			window.LA1TV_GLOBAL = {
				SERVER_DATA: <?=json_encode(isset($pageData) ? $pageData : array())?>
			};
		</script>
		<?php if (isset($cssFiles)): ?>
		<?php foreach($cssFiles as $a): ?>
		<link href="<?=e($a);?><?=e(isset($version) ? "?v=".$version : "");?>" rel="stylesheet" type="text/css">
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (isset($jsFiles)): ?>
		<?php foreach($jsFiles as $a): ?>
		<script src="<?=e($a);?><?=e(isset($version) ? "?v=".$version : "");?>"></script>
		<?php endforeach; ?>
		<?php endif; ?>
	</head>
	<body>
		@yield('body', '')
	</body>
</html>
