<?php
	// TODO: temporary
	$editing = false;
	$cancelUri = "";
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$editing?"Edit":"Create"?> Playlist</h3>
	</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="control-label">Dropdown</label>
			<div class="form-control ajax-select" data-datasourceuri="<?=URL::to("/admin/media/ajaxselect");?>" data-destinationname="dropdown_id" data-chosenitemtext="">
				
			</div>
			<input type="hidden" data-virtualform="1" class="form-control" name="dropdown_id" value="">
		</div>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<?=FormHelpers::getFormSubmitButton(1, ($editing?"Update":"Create")." Playlist", "", true, "");?>
		</div>
		<div class="pull-right">
			<a type="button" class="btn btn-default" data-confirm="Are you sure you want to cancel?" href="<?=e($cancelUri)?>">Cancel</a>
		</div>
	</div>
</div>