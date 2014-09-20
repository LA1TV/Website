<?php if (!is_null($coverImageUri)): ?>
<div class="cover-image-container">
	<img class="img-responsive img-rounded" src="<?=e($coverImageUri);?>">
</div>
<?php endif; ?>
<h1 class="no-top-margin"><?=e($playlistTitle);?></h1>
<?php if (!is_null($playlistDescription)): ?>
<p class="description"><?=e($playlistDescription);?></p>
<?php endif; ?>
<div class="playlist playlist-element">
	<div class="playlist-table-container">
		<table class="playlist-table table table-bordered table-striped table-hover">
			<tbody>
				<?php foreach($playlistTableData as $row):?>
				<tr>
					<td class="col-episode-no"><?=e($row['episodeNo'])?>.</td>
					<td class="col-thumbnail"><a href="<?=e($row['uri']);?>"><img class="img-responsive" src="<?=e($row['thumbnailUri']);?>"/></a></td>
					<?php if (is_null($row['playlistName'])): ?>
					<td class="col-title"><?=e($row['title']);?></td>
					<?php else: ?>
					<td class="col-title clearfix">
						<div class="subtitle"><span class="label label-info"><?=e($row['playlistName']);?></div></div>
						<div class="title"><?=e($row['title']);?></div>
						<?php if (!is_null($row['description'])): ?>
						<div class="description"><?=e($row['description']);?></div>
						<?php endif; ?>
					</td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
		</table>
	</div>
</div>
