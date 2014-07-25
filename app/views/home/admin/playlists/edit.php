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
		
		<div class="form-control reordable-list">
			<div class="list-container">
				<div class="list-row" data-highlight-state="0">
					<div class="cell cell-no">9.</div>
					<div class="cell cell-content">Some programme</div>
					<div class="cell cell-options"><div class="option option-drag">[DRAG]</div></div>
				</div>
				<div class="list-row" data-highlight-state="1">
					<div class="cell cell-no">10.</div>
					<div class="cell cell-content">Some programme</div>
					<div class="cell cell-options"><div class="option option-drag">[DRAG]</div></div>
				</div>
				<div class="list-row" data-highlight-state="0">
					<div class="cell cell-no">11.</div>
					<div class="cell cell-content">Some programme</div>
					<div class="cell cell-options"><div class="option option-drag">[DRAG]</div></div>
				</div>
			</div>
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