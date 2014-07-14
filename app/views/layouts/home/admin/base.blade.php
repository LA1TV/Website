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
	<body data-baseurl="<?=e($baseUrl)?>" data-csrftoken="<?=e($csrfToken)?>">
		@yield('content')
		<div id="footer">
			<div class="container">
				<p class="text-muted footer-txt">Developed by Tom Jenkinson, Ben Freke and Jack Croft for LA1:TV.</p>
			</div>
		</div>
	</body>
</html>