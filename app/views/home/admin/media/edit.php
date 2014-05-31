<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Media</h3>
	</div>
	<div class="panel-body">
		<div class="checkbox">
			<label>
				<input type="checkbox" data-virtualform="1" name="enabled" value="y" <?=$form['enabled']==="y" ? "checked":""?> > Enabled
			</label>
		</div>
		<div class="form-group">
			<label class="control-label">Name</label>
			<input type="text" data-virtualform="1" class="form-control" name="name" value="<?=e($form['name'])?>">
		</div>
		<div class="form-group">
			<label class="control-label">Description (Optional)</label>
			<textarea data-virtualform="1" class="form-control" name="description"><?=e($form['description'])?></textarea>
		</div>
		<div class="form-group">
			<label class="control-label">Cover Image (Optional)</label>
			<?=FormHelpers::getFileUploadElement("cover-image-id", AllowedFileTypesHelper::getImages(), $form['cover-image-file-name'], $form['cover-image-file-size'], $form['cover-image-id']);?>
		</div>
		<div class="form-group">
			<label class="control-label">Side Banners Image (Optional)</label>
			<?=FormHelpers::getFileUploadElement("side-banners-image-id", AllowedFileTypesHelper::getImages(), $form['side-banners-image-file-name'], $form['side-banners-image-file-size'], $form['side-banners-image-id']);?>
		</div>
		
		<div class="panel-group custom-accordian">
			<div class="panel panel-default vod-panel">
				<div class="panel-heading">
					<h4 class="panel-title">Video On Demand</h4>
				</div>
				<div class="panel-collapse collapse">
					<div class="panel-body">
						<div class="disabled-container">
							<button class="btn btn-primary enable-button">Add Video On Demand</button>
						</div>
						<div class="enabled-container">
							<input type="hidden" data-virtualform="1" name="vod-added" value="1"> 
							<button class="btn btn-default disable-button">Remove Video On Demand</button>
							<div class="checkbox">
								<label>
									<input type="checkbox" data-virtualform="1" name="vod-enabled" value="y" <?=$form['vod-enabled']==="y"?"checked":""?>> Enabled
								</label>
							</div>
							<div class="form-group">
								<label class="control-label">Name</label>
								<input type="text" data-virtualform="1" class="form-control" name="vod-name" value=<?=e($form['vod-name'])?>>
							</div>
							<div class="form-group">
								<label class="control-label">Description (Optional)</label>
								<textarea data-virtualform="1" class="form-control" name="vod-description"><?=e($form['vod-description'])?></textarea>
							</div>
							<div class="form-group">
								<label class="control-label">Video</label>
								<?=FormHelpers::getFileUploadElement("vod-video-id", AllowedFileTypesHelper::getVideos(), $form['vod-video-file-name'], $form['vod-video-file-size'], $form['vod-video-id']);?>
							</div>
							<div class="form-group">
								<label class="control-label">Time Recorded (Optional)</label>
								<input type="datetime-local" data-virtualform="1" class="form-control" name="vod-time-recorded" value="<?=e($form['vod-time-recorded'])?>">
							</div>
							<div class="form-group">
								<label class="control-label">Scheduled Publish Time (Optional)</label>
								<input type="datetime-local" data-virtualform="1" class="form-control" name="vod-publish-time" value="<?=e($form['vod-publish-time'])?>">
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" data-virtualform="1" name="vod-live-recording" value="y" <?=$form['vod-live-recording']==="y"?"checked":""?>> Is Live Recording
								</label>
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
						<div class="disabled-container">
							<button class="btn btn-primary enable-button">Add Live Stream</button>
						</div>
						<div class="enabled-container">
							<input type="hidden" data-virtualform="1" name="stream-added" value="1"> 
							<button class="btn btn-default disable-button">Remove Live Stream</button>
							<div class="checkbox">
								<label>
									<input type="checkbox" data-virtualform="1" name="stream-enabled" value="y" <?=$form['stream-enabled']==="y"?"checked":""?>> Enabled
								</label>
							</div>
							<div class="form-group">
								<label class="control-label">Name</label>
								<input type="text" data-virtualform="1" class="form-control" name="stream-name" value="<?=e($form['stream-name'])?>">
							</div>
							<div class="form-group">
								<label class="control-label">Description (Optional)</label>
								<textarea data-virtualform="1" class="form-control" name="stream-description" value="<?=e($form['stream-description'])?>"></textarea>
							</div>
							<div class="form-group">
								<label class="control-label">Scheduled Live Time (Optional)</label>
								<input type="datetime-local" data-virtualform="1" class="form-control" name="stream-live-time" value="<?=e($form['stream-live-time'])?>">
							</div>
							<div class="form-group">
								<label class="control-label">Stream</label>
								<select class="form-control" name="stream-stream-id">
									<?php foreach($streamOptions as $a): ?>
									<option value="<?=e($a['id'])?>" <?=$a['id'] === $form['stream-stream-id']?"selected":""?>><?=e($a['name'])?></option>
									<?php endforeach; ?>
								</select>
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