<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Edit Media</h3>
	</div>
	<div class="panel-body">
		<div class="form-group">
			<label class="control-label">Name</label>
			<input type="text" data-virtualform="1" class="form-control" name="name">
		</div>
		<div class="form-group">
			<label class="control-label">Description</label>
			<input type="text" data-virtualform="1" class="form-control" name="description">
		</div>
		<div class="form-group">
			<label class="control-label">Cover Image</label>
			<div class="form-control ajax-upload" data-name="cover-image"></div>
			<input type="hidden" data-virtualform="1" name="cover-image-id" value="" />
		</div>
		<div class="form-group">
			<label class="control-label">Side Banners Image</label>
			<div class="form-control ajax-upload" data-name="side-banners-image"></div>
			<input type="hidden" data-virtualform="1" name="side-banners-image-id" value="" />
		</div>
	</div>
	<div class="panel-footer clearfix">
		<div class="pull-left">
			<button type="button" data-virtualform="1" data-virtualformsubmit="1" data-virtualformsubmitmethod="post" data-virtualformsubmitaction="" class="btn btn-primary">Create Media</button>
		</div>
		<div class="pull-right">
			<button type="button" class="btn btn-default">Cancel</button>
		</div>
	</div>
</div>