@extends('layouts.base')

@section('body')
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
				<a class="navbar-brand" href="<?=e($homeUri);?>"><img class="img-responsive" src="<?=asset("assets/img/logo.png");?>"/></a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					@yield('navbarList', '')
				</ul>
				<a class="btn btn-default navbar-btn navbar-right" href="<?=e($loggedIn ? $logoutUri : $loginUri);?>"><?=e($loggedIn ? "Logout" : "[FB] Login");?></a>
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
@stop
