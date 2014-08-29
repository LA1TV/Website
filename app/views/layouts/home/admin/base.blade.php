<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="robots" content="noindex, nofollow">
		<?= stylesheet_link_tag("includes/admin/application") ?>
		<?= javascript_include_tag("includes/admin/application") ?>
	</head>
	<body data-baseurl="<?=e($baseUrl)?>" data-assetsbaseurl="<?=e(asset(""))?>" data-csrftoken="<?=e($csrfToken)?>">
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
						@yield('navbarList', '')
					</ul>
					<a class="btn btn-info navbar-btn navbar-right" href="<?=e(URL::to(Auth::isLoggedIn()?"/admin/login/logout":"/admin/login"))?>"><?=!Auth::isLoggedIn()?"Login":"Log Out"?></a>
				</div>
			</div>
		</div>
		@yield('content')
		<div id="footer">
			<div class="container">
				<p class="text-muted footer-txt">The custom built CMS for LA1:TV.</p>
			</div>
		</div>
	</body>
</html>