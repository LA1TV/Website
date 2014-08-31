<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?=$title?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<meta name="description" content="<?=e($description)?>">
		<meta name="author" content="">
		<?= stylesheet_link_tag("includes/application") ?>
		<?= javascript_include_tag("includes/application") ?>
	</head>
	<body data-baseurl="<?=e($baseUrl)?>" data-assetsbaseurl="<?=e(asset(""))?>" data-csrftoken="<?=e($csrfToken)?>">
		<div class="container">
			<nav class="navbar navbar-default" role="navigation">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="<?=Config::get("custom.base_url");?>"><img class="img-responsive" src="<?=asset("assets/img/logo.png");?>"/></a>
					</div>
					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav">
							@yield('navbarList', '')
						</ul>
						<a class="btn btn-default navbar-btn navbar-right" href="">Login With Facebook</a>
					</div>
				</div>
			</nav>
		</div>
		@yield('content')
		<div id="footer">
			<div class="container">
				<p class="text-muted footer-txt">&copy; LA1:TV [SOME YEAR]. OPEN SOURCE. Code here.</p>
			</div>
		</div>
	</body>
</html>