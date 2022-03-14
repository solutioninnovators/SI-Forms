<?php if($markup): ?>
	<?= $formHeader ?>
<?php endif ?>
	<?= $beforeForm ?>
	<div class="gGrid">
		<?php foreach($fields as $field): ?>
			<?= $field ?>
		<?php endforeach ?>
	</div>
	<?= $afterForm ?>
<?php if($markup): ?>
	<?= $formFooter ?>
<?php endif ?>