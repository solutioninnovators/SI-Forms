<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<?php if($value): ?>
			<?= $sanitizer->entities($value) ?>
		<?php else: ?>
			<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
		<?php endif ?>
	<?php else: ?>
		<input maxlength="<?= $maxLength ?>" name="<?= $name ?>" id="<?= $name ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $placeholder ?>" value="<?= $sanitizer->entities($value) ?>" data-date-format="<?= $jsDateFormat ?>" <?= $disabled ? 'disabled' : '' ?> />
	<?php endif ?>

<?php include('../Field/footer.php') ?>