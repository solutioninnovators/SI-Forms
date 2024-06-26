<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>" value="<?= $sanitizer->entities1($value) ?>" <?= $disabled ? 'disabled' : '' ?> />

			<?php if($value): ?>
				<?= $sanitizer->entities1($value) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<input
            <?= $formAttribute ?>
            type="text"
            maxlength="<?= $sanitizer->entities1($maxLength) ?>"
            name="<?= $sanitizer->entities1($name) ?>"
            <?= $id ? 'id="input_' . $sanitizer->entities1($id).'"' : '' ?>
            class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?> <?= $sanitizer->entities1($inputClasses) ?>"
            placeholder="<?= $sanitizer->entities1($placeholder) ?>"
            value="<?= $sanitizer->entities1($value) ?>"
            data-date-format="<?= $sanitizer->entities1($jsDateFormat) ?>"
            data-min-date="<?= $sanitizer->entities1($minDate) ?>"
            <?= $disabled ? 'disabled' : '' ?>
            <?= !$autocomplete ? 'autocomplete="off"' : '' ?>
        />
	<?php endif ?>

<?php include('../Field/footer.php') ?>