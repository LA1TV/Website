@extends('layouts.home.base')

@section('navbarList')
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Shows <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="">Item 1</a></li>
	</ul>
</li>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Playlists <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="">Item 1</a></li>
	</ul>
</li>
<li><a href="">Live Guide</a></li>
<li><a href="">Blog</a></li>
<li><a href="">Contact</a></li>
<li><a href="">About</a></li>
@stop

@section('content')
<div id="main-content" class="container page-<?=$cssPageId?>">
	<?=$content?>
</div>
@stop