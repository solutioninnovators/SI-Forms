<div
	class="field buttonField<?= $fullWidth ? ' buttonField_fullWidth' : '' ?>"
	data-field-name="<?= $sanitizer->entities1($name) ?>"
	<?php if(count($dependsOn)): ?>
		data-depends-on="<?= $sanitizer->entities1(implode(' ', $dependsOn)) ?>"
	<?php endif ?>
>
	<?php if($href): ?>
		<a class="btn <?= $sanitizer->entities1($btnClasses) ?>" href="<?= $sanitizer->entities1($href) ?>" <?= $sanitizer->entities1($attributeString) ?> <?= $disabled ? 'disabled' : '' ?>>
			<?php if($icon): ?>
				<i class="fa fa-fw fa-<?= $sanitizer->entities1($icon) ?>"></i>
			<?php endif ?>
			<?= $sanitizer->entities1($label) ?>
		</a>
	<?php else: ?>
		<button
			<?= $formAttribute ?>
			type="<?= $sanitizer->entities1($type) ?>"
			class="btn <?= $sanitizer->entities1($btnClasses) ?> <?= $sanitizer->entities1($inputClasses) ?>"
			name="<?= $sanitizer->entities1($name) ?>"
			value="<?= $sanitizer->entities1($value) ?>"
			<?= $disabled ? 'disabled' : '' ?>
			<?= $attributeString ?>
            <?php foreach($extraAttributes as $attr => $val): ?>
                <?= $sanitizer->entities1($attr) ?>="<?= $sanitizer->entities1($val) ?>"
            <?php endforeach?>
		>
			<?php if($icon): ?>
				<i class="fa fa-fw fa-<?= $sanitizer->entities1($icon) ?>"></i>
			<?php endif ?>
			<?= $sanitizer->entities1($label) ?>
		</button>
	<?php endif ?>
</div>
