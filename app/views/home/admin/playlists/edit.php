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
		<?=FormHelpers::getAjaxSelectInput(1, "Test Dropdown", "test_dropdown_id", "", null, URL::to("/admin/media/ajaxselect"), "") ?>
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