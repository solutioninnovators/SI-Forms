<div class="field buttonField <?= $fullWidth ? 'buttonField_fullWidth' : '' ?>">
	<?php if($href): ?>
		<a class="btn <?= $btnClasses ?>" href="<?= $href ?>" <?= $attributeString ?>><i class="fa fa-fw fa-<?= $icon ?>"></i> <?= $label ?></a>
	<?php else: ?>
		<button type="<?= $type ?>" class="btn <?= $btnClasses ?>" name="<?= $name ?>" <?= $attributeString ?> value="<?= $value ?>"><i class="fa fa-<?= $icon ?>"></i> <?= $label ?></button>
	<?php endif ?>
</div>
