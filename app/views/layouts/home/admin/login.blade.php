@extends('layouts.home.admin.base')

@section('content')
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?=Config::get("custom.admin_base_url");?>">LA1:TV CMS</a>
		</div>
	</div>
</div>
<div id="main-content" class="container page-<?=$cssPageId?>">
	<?=$content?>
</div>
@stop