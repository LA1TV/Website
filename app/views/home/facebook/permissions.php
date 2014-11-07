<h1>Facebook Permissions</h1>
<?php if ($loggedIn): ?>
<p>Listed below are the permissions we would like in order to provide you with the best experience.</p>
<p>If there are any permissions missing please provide them by clicking on the corresponding button.</p>
<table class="table table-bordered table-hover permissions-table">
	<thead>
		<tr>
			<th class="status-col">Status</th>
			<th class="permission-col">Permission</th>
			<th class="reason-col">Why We Would Like This</th>
		</tr>
	</thead>
	<tbody>
<?php foreach($permissionsTableContent as $a): ?>
		<tr>
			<td class="status-col">
				<div class="status-icon">
				<?php if ($a['granted']): ?>
					<span class="glyphicon glyphicon-ok status-icon-tick"></span>
				<?php else: ?>
					<span class="glyphicon glyphicon-remove status-icon-cross"></span>
				<?php endif; ?>
				</div>
				<?php if (!$a['granted']): ?>
				<a class="btn btn-primary btn-sm" href="<?=e($a['requestPermissionUri']);?>">Provide This</button>
				<?php endif; ?>
			</td>
			<td class="permission-col"><?=e($a['name']);?></td>
			<td class="reason-col"><?=e($a['description']);?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>You need to be logged in to view this page.</p>
<?php endif; ?>