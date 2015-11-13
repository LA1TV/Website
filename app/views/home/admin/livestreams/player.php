<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Watch Stream: "<?=e($streamName);?>"</h3>
	</div>
	<div class="panel-body">
		<?php if (!$streamAccessible): ?>
		<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-info-sign"></span> This stream is currently not accessible on the site. It needs to be marked as being live.</div>
		<?php endif; ?>
		<div class="player-container" data-cover-art-uri="<?=e($coverArtUri)?>" data-stream-uris="<?=e($streamUris)?>"></div>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<a type="button" class="btn btn-primary" href="<?=e($backUri)?>">Go Back</a>
		</div>
	</div>
</div>