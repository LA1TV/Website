<?php
$nav = array(
	"dashboard"		=> array("Dashboard", Config::get("custom.admin_base_url")."/dashboard", false),
	"media"			=> array("Media", Config::get("custom.admin_base_url")."/media", false),
	"playlists"		=> array("Playlists/Series", Config::get("custom.admin_base_url")."/playlists", false),
	"livestreams"	=> array("Live Streams", Config::get("custom.admin_base_url")."/livestreams", false),
	"comments"		=> array("Comments", Config::get("custom.admin_base_url")."/comments", false),
	"siteusers"		=> array("Site Users", Config::get("custom.admin_base_url")."/siteusers", false),
	"users"			=> array("CMS Users", Config::get("custom.admin_base_url")."/users", false),
	"permissions"	=> array("Permissions", Config::get("custom.admin_base_url")."/permissions", false),
	"monitoring"	=> array("Monitoring", Config::get("custom.admin_base_url")."/monitoring", false)
);

// make the current page active in the nav bar
if (isset($nav[$currentNavPage])) {
	$nav[$currentNavPage][2] = true;
}

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
	<body data-baseurl="<?=e($baseUrl)?>" data-csrftoken="<?=e($csrfToken)?>">
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?=Config::get("custom.admin_base_url");?>">LA1:TV CMS</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<?php foreach(array("dashboard", "media", "playlists", "livestreams", "comments") as $b):
							$a = $nav[$b];
						?>
						<li class="<?=$a[2]?"active":""?>"><a href="<?=URL::to($a[1])?>"><?=e($a[0])?></a></li>
						<?php endforeach; ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">More <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php foreach(array("siteusers", "users", "permissions", "monitoring") as $b):
									$a = $nav[$b];
								?>
								<li><a href="<?=URL::to($a[1])?>"><?=e($a[0])?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div id="main-content" class="container page-<?=$cssPageId?>">
			<?=$content?>
		</div>
		<div id="footer">
			<div class="container">
				<p class="text-muted footer-txt">Developed by Tom Jenkinson, Ben Freke and Jack Croft for LA1:TV.</p>
			</div>
		</div>
	</body>
</html>
