<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Edit Media</h3>
	</div>
	<form role="form" method="post">
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label">Name</label>
				<input type="text" class="form-control" name="name">
			</div>
			<div class="form-group">
				<label class="control-label">Description</label>
				<input type="text" class="form-control" name="description">
			</div>
			<div class="form-group">
				<label class="control-label">Cover Image</label>
				<div class="form-control ajax-upload" data-name="cover-image"></div>
				<input type="hidden" name="cover-image-id" value="" />
			</div>
			<div class="form-group">
				<label class="control-label">Side Banners Image</label>
				<div class="form-control ajax-upload" data-name="side-banners-image"></div>
				<input type="hidden" name="side-banners-image-id" value="" />
			</div>
		</div>
		<div class="panel-footer clearfix">
			<div class="pull-left">
				<button type="submit" class="btn btn-primary">Create Media</button>
			</div>
			<div class="pull-right">
				<button type="button" class="btn btn-default">Cancel</button>
			</div>
		</div>
	</form>
</div>