<h1>Contact Us</h1>
<div class="row">
	<div class="col-md-6">
		<dl class="dl-horizontal contact-dl">
			<dt>General Enquiries</dt>
			<dd><a href="mailto:<?=e($contactEmail);?>"><?=e($contactEmail);?></a></dd>
			<dt>Technical Support</dt>
			<dd><a href="mailto:<?=e($developmentEmail);?>"><?=e($developmentEmail);?></a></dd>
			<dt>Facebook</dt>
			<dd><a href="<?=e($facebookPageUri);?>" target="_blank"><?=e($facebookPageUri);?></a></dd>
			<dt>Twitter</dt>
			<dd><a href="<?=e($twitterPageUri);?>" target="_blank"><?=e($twitterPageUri);?></a></dd>
		</dl>
		<?php if (!is_null($twitterWidgetId)): ?>
		<div class="image-container">
			<img class="img-responsive img-rounded" src="<?=asset("assets/img/roses-photo.jpg");?>"/>
		</div>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<?php if (!is_null($twitterWidgetId)): ?>
		<div class="twitter-timeline-container" data-twitter-widget-height="500" data-twitter-widget-id="<?=e($twitterWidgetId);?>"></div>
		<?php else: ?>
		<div class="image-container">
			<img class="img-responsive img-rounded" src="<?=asset("assets/img/roses-photo.jpg");?>"/>
		<div class="image-container">
		<?php endif; ?>
	</div>
</div>