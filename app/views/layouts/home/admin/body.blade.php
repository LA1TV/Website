@extends('layouts.base')

@section('body')
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?=Config::get("custom.admin_base_url");?>">LA1TV CMS</a>
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
		<p class="text-muted footer-txt">The custom built content management system for LA1TV.</p>
	</div>
</div>
@stop