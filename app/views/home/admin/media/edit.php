<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Media</h3>
	</div>
	<div class="panel-body">
		<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "enabled");?>">
			<div class="checkbox">
				<label>
					<input type="checkbox" data-virtualform="1" name="enabled" value="y" <?=$form['enabled']==="y" ? "checked":""?> > Enabled
				</label>
			</div>
			<?=FormHelpers::getErrMsgHTML($formErrors, "enabled");?>
		</div>
		<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "name");?>">
			<label class="control-label">Name</label>
			<input type="text" data-virtualform="1" class="form-control" name="name" value="<?=e($form['name'])?>">
			<?=FormHelpers::getErrMsgHTML($formErrors, "name");?>
		</div>
		<div class="form-group" <?=FormHelpers::getErrCSS($formErrors, "description");?>>
			<label class="control-label">Description (Optional)</label>
			<textarea data-virtualform="1" class="form-control" name="description"><?=e($form['description'])?></textarea>
			<?=FormHelpers::getErrMsgHTML($formErrors, "description");?>
		</div>
		<div class="form-group" <?=FormHelpers::getErrCSS($formErrors, "cover-image-id");?>>
			<label class="control-label">Cover Image (Optional)</label>
			<?=FormHelpers::getFileUploadElement("cover-image-id", AllowedFileTypesHelper::getImages(), $form['cover-image-file-name'], $form['cover-image-file-size'], $form['cover-image-id']);?>
			<?=FormHelpers::getErrMsgHTML($formErrors, "cover-image-id");?>
		</div>
		<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "side-banners-image-id");?>">
			<label class="control-label">Side Banners Image (Optional)</label>
			<?=FormHelpers::getFileUploadElement("side-banners-image-id", AllowedFileTypesHelper::getImages(), $form['side-banners-image-file-name'], $form['side-banners-image-file-size'], $form['side-banners-image-id']);?>
			<?=FormHelpers::getErrMsgHTML($formErrors, "side-banners-image-id");?>
		</div>
		
		<div class="panel-group custom-accordian">
			<div class="panel panel-default vod-panel">
				<div class="panel-heading">
					<h4 class="panel-title">Video On Demand</h4>
				</div>
				<div class="panel-collapse collapse">
					<div class="panel-body">
						<input type="hidden" class="enabled-input" data-virtualform="1" name="vod-added" value="<?=e($form['vod-added'])?>"> 
						<div class="disabled-container">
							<button class="btn btn-primary enable-button">Add Video On Demand</button>
						</div>
						<div class="enabled-container">
							<button class="btn btn-default disable-button">Remove Video On Demand</button>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "vod-enabled");?>">
								<div class="checkbox">
									<label>
										<input type="checkbox" data-virtualform="1" name="vod-enabled" value="y" <?=$form['vod-enabled']==="y"?"checked":""?>> Enabled
									</label>
								</div>
								<?=FormHelpers::getErrMsgHTML($formErrors, "vod-enabled");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "vod-name");?>">
								<label class="control-label">Name (Optional)</label>
								<input type="text" data-virtualform="1" class="form-control" name="vod-name" value=<?=e($form['vod-name'])?>>
								<?=FormHelpers::getErrMsgHTML($formErrors, "vod-name");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "vod-description");?>">
								<label class="control-label">Description (Optional)</label>
								<textarea data-virtualform="1" class="form-control" name="vod-description"><?=e($form['vod-description'])?></textarea>
								<?=FormHelpers::getErrMsgHTML($formErrors, "vod-description");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "vod-video-id");?>">
								<label class="control-label">Video</label>
								<?=FormHelpers::getFileUploadElement("vod-video-id", AllowedFileTypesHelper::getVideos(), $form['vod-video-file-name'], $form['vod-video-file-size'], $form['vod-video-id']);?>
								<?=FormHelpers::getErrMsgHTML($formErrors, "vod-video-id");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "vod-time-recorded");?>">
								<label class="control-label">Time Recorded (Optional)</label>
								<input type="datetime-local" data-virtualform="1" class="form-control" name="vod-time-recorded" value="<?=e($form['vod-time-recorded'])?>">
								<?=FormHelpers::getErrMsgHTML($formErrors, "vod-time-recorded");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "vod-publish-time");?>">
								<label class="control-label">Scheduled Publish Time (Optional)</label>
								<input type="datetime-local" data-virtualform="1" class="form-control" name="vod-publish-time" value="<?=e($form['vod-publish-time'])?>">
								<?=FormHelpers::getErrMsgHTML($formErrors, "vod-publish-time");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "vod-live-recording");?>">
								<div class="checkbox">
									<label>
										<input type="checkbox" data-virtualform="1" name="vod-live-recording" value="y" <?=$form['vod-live-recording']==="y"?"checked":""?>> Is Live Recording
									</label>
								</div>
								<?=FormHelpers::getErrMsgHTML($formErrors, "vod-live-recording");?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Live Stream</h4>
				</div>
				<div class="panel-collapse collapse live-stream-panel">
					<div class="panel-body">
						<input type="hidden" class="enabled-input" data-virtualform="1" name="stream-added" value="<?=e($form['stream-added'])?>"> 
						<div class="disabled-container">
							<button class="btn btn-primary enable-button">Add Live Stream</button>
						</div>
						<div class="enabled-container">
							<button class="btn btn-default disable-button">Remove Live Stream</button>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "stream-enabled");?>">
								<div class="checkbox">
									<label>
										<input type="checkbox" data-virtualform="1" name="stream-enabled" value="y" <?=$form['stream-enabled']==="y"?"checked":""?>> Enabled
									</label>
								</div>
								<?=FormHelpers::getErrMsgHTML($formErrors, "stream-enabled");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "stream-name");?>">
								<label class="control-label">Name (Optional)</label>
								<input type="text" data-virtualform="1" class="form-control" name="stream-name" value="<?=e($form['stream-name'])?>">
								<?=FormHelpers::getErrMsgHTML($formErrors, "stream-name");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "stream-description");?>">
								<label class="control-label">Description (Optional)</label>
								<textarea data-virtualform="1" class="form-control" name="stream-description" value="<?=e($form['stream-description'])?>"></textarea>
								<?=FormHelpers::getErrMsgHTML($formErrors, "stream-description");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "stream-live-time");?>">
								<label class="control-label">Scheduled Live Time (Optional)</label>
								<input type="datetime-local" data-virtualform="1" class="form-control" name="stream-live-time" value="<?=e($form['stream-live-time'])?>">
								<?=FormHelpers::getErrMsgHTML($formErrors, "stream-live-time");?>
							</div>
							<div class="form-group <?=FormHelpers::getErrCSS($formErrors, "stream-stream-id");?>">
								<label class="control-label">Stream</label>
								<select class="form-control" data-virtualform="1" name="stream-stream-id">
									<?php foreach($streamOptions as $a): ?>
									<option value="<?=e($a['id'])?>" <?=$a['id'] === $form['stream-stream-id']?"selected":""?>><?=e($a['name'])?></option>
									<?php endforeach; ?>
								</select>
								<?=FormHelpers::getErrMsgHTML($formErrors, "stream-stream-id");?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<button type="button" data-virtualform="1" data-virtualformsubmit="1" data-virtualformsubmitmethod="post" data-virtualformsubmitaction="" class="btn btn-primary"><?=$editing?"Update":"Create"?> Media</button>
		</div>
		<div class="pull-right">
			<button type="button" class="btn btn-default">Cancel</button>
		</div>
	</div>
</div>