@extends('layouts.home.body')

@section('promo')
<div class="promo-container" data-ajaxuri="<?=e($promoAjaxUri);?>"></div>
@stop

@section('navbarList')
<?php if (count($showsDropdown) > 0): ?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Shows <b class="caret"></b></a>
	<ul class="dropdown-menu">
	<?php foreach($showsDropdown as $a): ?>
		<li><a href="<?=e($a['uri']);?>"><?=e($a['text']);?></a></li>
	<?php endforeach; ?>
		<li class="divider"></li>
		<li><a href="<?=e($showsUri);?>">View All</a></li>
	</ul>
</li>
<?php else: ?>
<li><a href="<?=e($showsUri);?>">Shows</a></li>
<?php endif; ?>
<?php if (count($playlistsDropdown) > 0): ?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Playlists <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<?php foreach($playlistsDropdown as $a): ?>
		<li><a href="<?=e($a['uri']);?>"><?=e($a['text']);?></a></li>
	<?php endforeach; ?>
		<li class="divider"></li>
		<li><a href="<?=e($playlistsUri);?>">View All</a></li>
	</ul>
</li>
<?php else: ?>
<li><a href="<?=e($playlistsUri);?>">Playlists</a></li>
<?php endif; ?>
<li><a href="<?=e($guideUri);?>">Live Guide</a></li>
<li><a href="<?=e($blogUri);?>">Blog</a></li>
<li><a href="<?=e($contactUri);?>">Contact</a></li>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Partners <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="http://scan.lusu.co.uk/" target="_blank">SCAN</a></li>
		<li><a href="http://bailriggfm.co.uk/" target="_blank">Bailrigg FM</a></li>
		<li><a href="http://lusu.co.uk/" target="_blank">LUSU</a></li>
	</ul>
</li>
@stop

@section('content')
<div id="main-content" class="container page-<?=$cssPageId?>">
	<?=$content?>
</div>
@stop