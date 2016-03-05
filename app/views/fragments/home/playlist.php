<div class="playlist-element  <?=!is_null($headerRowData)?"with-header":""?>">
	<?php if (!is_null($headerRowData)): ?>
	<div class="button-row clearfix">
		<div class="buttons">
			<?php if (!is_null($headerRowData['seriesUri'])): ?>
			<div class="item">
				<a class="btn btn-default btn-xs" href="<?=e($headerRowData['seriesUri'])?>">View All Series</a>
			</div>
			<?php endif; ?>
			<?php if (!is_null($headerRowData['navButtons'])):?>
			<?php if (isset($headerRowData['navButtons']['showAutoPlayButton']) && $headerRowData['navButtons']['showAutoPlayButton']): ?>
			<div class="item auto-continue-btn-item"></div>
			<?php endif; ?>
			<div class="item">
				<?php if (!is_null($headerRowData['navButtons']['previousItemUri'])): ?>
				<a href="<?=e($headerRowData['navButtons']['previousItemUri']);?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-backward"></span></a>
				<?php else: ?>
				<button disabled type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-backward"></span></button>
				<?php endif; ?>
			</div>
			<div class="item">
				<?php if (!is_null($headerRowData['navButtons']['nextItemUri'])): ?>
				<a href="<?=e($headerRowData['navButtons']['nextItemUri']);?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-forward"></span></a>
				<?php else: ?>
				<button disabled type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-fast-forward"></span></button>
			<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<h2 class="playlist-title"><?=e($headerRowData['title']);?></h2>
	</div>
	<?php endif; ?>
	<div class="playlist-table-container">
		<div class="playlist-list">
			<?php foreach($tableData as $row):?>
			<table class="playlist-table table table-hover">
				<tbody>
					<tr class="<?=$row['active'] ? "chosen" : ""?> zoom-animation-container" data-link="<?=e($row['uri']);?>">
						<?php if (!is_null($row['episodeNo'])): ?>
						<td class="col-episode-no"><?=e($row['episodeNo'])?>.</td>
						<?php endif; ?>
						<td class="col-thumbnail" data-thumbnailuri="<?=e($row['thumbnailUri']);?>">
							<div class="height-helper"></div>
							<div class="image-container">
								<div class="image-holder zoom-animation"></div>
							</div>
							<?php if (!is_null($row['thumbnailFooter'])): ?>
							<div class="footer">
								<div><?=$row['thumbnailFooter']['isLive']?"Live":"Available"?></div>
								<div><?=e($row['thumbnailFooter']['dateTxt']);?></div>
							</div>
							<?php elseif(!is_null($row['duration'])): ?>
							<div class="duration"><?=e($row['duration']);?></div>
							<?php endif; ?>
							<a class="hyperlink" href="<?=e($row['uri']);?>"></a>
						</td>
						<td class="col-title clearfix">
							<div class="title"><?=e($row['title']);?></div>
							<?php if (!is_null($row['stats'])): ?>
							<div class="stats-bar">
								<?php if (!is_null($row['stats']['viewCount'])): ?>
								<div class="item">
									<span class="glyphicon glyphicon-eye-open"></span> <?=e($row['stats']['viewCount'])?> <?=$row['stats']['viewCount'] !== 1 ? "views" : "view"?>
								</div>
								<?php endif; ?>
								<?php if (!is_null($row['stats']['numLikes'])): ?>
								<div class="item">
									<span class="glyphicon glyphicon-thumbs-up"></span> <?=e($row['stats']['numLikes'])?> <?=$row['stats']['numLikes'] !== 1 ? "likes" : "like"?>
								</div>
								<?php endif; ?>
							</div>
							<?php endif; ?>
							<?php if (!is_null($row['escapedDescription'])): ?>
							<div class="description"><?=$row['escapedDescription'];?></div>
							<?php endif; ?>
							<?php if (!is_null($row['playlistName'])): ?>
							<div class="subtitle-filler"><?=e($row['playlistName']);?></div>
							<div class="subtitle"><?=e($row['playlistName']);?></div>
							<?php endif; ?>
						</td>
					</tr>
			</table>
			<?php endforeach; ?>
		</div>
	</div>
</div>
