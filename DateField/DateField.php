<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<?php if($value): ?>
			<?= $sanitizer->entities1($value) ?>
		<?php else: ?>
			<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
		<?php endif ?>
	<?php else: ?>
		<input maxlength="<?= $sanitizer->entities1($maxLength) ?>" name="<?= $sanitizer->entities1($name) ?>" id="input_<?= $sanitizer->entities1($id) ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $sanitizer->entities1($placeholder) ?>" value="<?= $sanitizer->entities1($value) ?>" data-date-format="<?= $sanitizer->entities1($jsDateFormat) ?>" <?= $disabled ? 'disabled' : '' ?> />
	<?php endif ?>

<?php include('../Field/footer.php') ?>