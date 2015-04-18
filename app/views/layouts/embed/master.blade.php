@extends('layouts.base')

@section('body')
<noscript>
	<div class="container no-js-msg-container">
		<div class="panel panel-danger no-js-panel">
			<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> Javascript Required</div>
				<div class="panel-body">
					<p><strong>This content requires javascript to be enabled in your web browser in order to function.</strong></p>
					<p>Please contact <a href="mailto:<?=e($supportEmail);?>"><?=e($supportEmail);?></a> for support.</p>
				</div>
			</div>
		</div>
	</div>
</noscript>
<div class="js-required">
	<div id="main-content" class="page-<?=$cssPageId?>">
		<?=$content?>
	</div>
</div>
@stop