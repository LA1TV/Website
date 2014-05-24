<?php
$nav = array(
	array("Home", "", false),
	array("Uploads", "uploads", false),
	array("Comments", "comments", false),
	array("Live Streams", "livestreams", false),
	array("Playlists/Series", "playlists", false),
	array("Permissions", "permissions", false),
	array("Monitoring", "monitoring", false)
);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="robots" content="noindex, nofollow">
		<?= stylesheet_link_tag("includes/admin/application") ?>
		<?= javascript_include_tag("includes/admin/application") ?>
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">LA1:TV CMS</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<?php foreach($nav as $a): ?>
						<li class="<?=$a[2]?"active":""?>"><a href="<?=URL::to($a[1])?>"><?=e($a[0])?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div id="footer">
			<div class="container">
				<p class="text-muted footer-txt">Developed by Tom Jenkinson, Ben Freeke and Jack Croft for LA1:TV.</p>
			</div>
		</div>
	</body>
</html>
