<div class="page-numbers-container">
	<ul class="pagination">
		<?php if (!is_null($prevUri)):?>
		<li><a href="<?=e($prevUri);?>" rel="prev">&laquo;</a></li>
		<?php else: ?>
		<li class="disabled"><span>&laquo;</span></li>
		<?php endif; ?>
		<?php foreach($numbers as $a): ?>
		<li<?=$a['active']?' class="active"':""?>><a href="<?=e($a['uri']);?>" rel="canonical"><?=e($a['num']);?></a></li>
		<?php endforeach; ?>
		<?php if (!is_null($nextUri)):?>
		<li><a href="<?=e($nextUri);?>" rel="next">&raquo;</a></li>
		<?php else: ?>
		<li class="disabled"><span>&raquo;</span></li>
		<?php endif; ?>
	</ul>
</div>