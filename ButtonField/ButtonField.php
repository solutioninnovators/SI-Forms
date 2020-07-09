<div class="field buttonField <?= $fullWidth ? 'buttonField_fullWidth' : '' ?>">
	<?php if($href): ?>
		<a class="btn <?= $sanitizer->entities1($btnClasses) ?>" href="<?= $sanitizer->entities1($href) ?>" <?= $sanitizer->entities1($attributeString) ?>><i class="fa fa-fw fa-<?= $sanitizer->entities1($icon) ?>"></i> <?= $sanitizer->entities1($label) ?></a>
	<?php else: ?>
		<button type="<?= $sanitizer->entities1($type) ?>" class="btn <?= $sanitizer->entities1($btnClasses) ?>" name="<?= $sanitizer->entities1($name) ?>" <?= $sanitizer->entities1($attributeString) ?> value="<?= $sanitizer->entities1($value) ?>"><i class="fa fa-fw fa-<?= $sanitizer->entities1($icon) ?>"></i> <?= $sanitizer->entities1($label) ?></button>
	<?php endif ?>
</div>
