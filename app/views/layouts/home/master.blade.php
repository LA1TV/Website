@extends('layouts.home.body')

@section('navbarList')
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Shows <b class="caret"></b></a>
	<ul class="dropdown-menu">
	<?php foreach($showsDropdown as $a): ?>
		<li><a href="<?=e($a['uri']);?>"><?=e($a['text']);?></a></li>
	<?php endforeach; ?>
	<?php if (count($showsDropdown) > 0): ?>
		<li class="divider"></li>
	<?php endif; ?>
		<li><a href="<?=e($showsUri);?>">View All</a></li>
	</ul>
</li>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Playlists <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<?php foreach($playlistsDropdown as $a): ?>
		<li><a href="<?=e($a['uri']);?>"><?=e($a['text']);?></a></li>
	<?php endforeach; ?>
	<?php if (count($playlistsDropdown) > 0): ?>
		<li class="divider"></li>
	<?php endif; ?>
		<li><a href="<?=e($playlistsUri);?>">View All</a></li>
	</ul>
</li>
<li><a href="<?=e($guideUri);?>">Live Guide</a></li>
<li><a href="<?=e($blogUri);?>">Blog</a></li>
<li><a href="<?=e($contactUri);?>">Contact</a></li>
<li><a href="<?=e($aboutUri);?>">About</a></li>
@stop

@section('content')
<div id="main-content" class="container page-<?=$cssPageId?>">
	<?=$content?>
</div>
@stop