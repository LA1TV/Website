@extends('layouts.home.body')

@section('no-js-msg')
<noscript>
	<div class="container no-js-msg-container">
		<div class="logo-container">
			<img class="img-responsive logo" alt="LA1:TV Logo" src="<?=asset("/assets/img/logo.png");?>"/>
		</div>
		<div class="panel panel-danger no-js-panel">
			<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> Javascript Required</div>
				<div class="panel-body">
					<p><strong>Our website requires javascript to be enabled in your web browser in order to function.</strong></p>
					<p>Please contact <a href="mailto:<?=e($supportEmail);?>"><?=e($supportEmail);?></a> for support and help with enabling this.</p>
				</div>
			</div>
		</div>
	</div>
</noscript>
@stop

@section('side-banners')
<div class="side-banners-container-container">
	<div class="container side-banners-container">
		<div class="side-banner-container side-banner-container-left" data-bg-url="<?=!is_null($sideBannersFillImageUrl) ? e($sideBannersFillImageUrl) : ""?>">
			<?php if (!is_null($sideBannersImageUrl)): ?>
			<div class="side-banner"><img src="<?=e($sideBannersImageUrl);?>"></div>
			<?php endif; ?>
		</div>
		<div class="side-banner-container side-banner-container-right" data-bg-url="<?=!is_null($sideBannersFillImageUrl) ? e($sideBannersFillImageUrl) : ""?>">
			<?php if (!is_null($sideBannersImageUrl)): ?>
			<div class="side-banner"><img src="<?=e($sideBannersImageUrl);?>"></div>
			<?php endif; ?>
		</div>
	</div>
	<div class="container side-banners-container side-banners-container-shadow"></div>
</div>
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
<li><a href="<?=e($guideUri);?>">Guide</a></li>
<?php if (count($liveStreamsDropdown) > 0): ?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Live Streams<b class="caret"></b></a>
	<ul class="dropdown-menu">
		<?php foreach($liveStreamsDropdown as $a): ?>
		<li><a href="<?=e($a['uri']);?>"><?=e($a['text']);?></a></li>
		<?php endforeach; ?>
	</ul>
</li>
<?php endif; ?>
<?php if (!is_null($wikiUri)): ?>
<li><a href="<?=e($wikiUri);?>">Wiki</a></li>
<?php endif; ?>
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
