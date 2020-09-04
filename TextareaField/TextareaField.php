<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($value): ?>
				<?= $sanitizer->entities1($value) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<textarea rows="<?= $sanitizer->entities1($rows) ?>" maxlength="<?= $sanitizer->entities1($maxLength) ?>" name="<?= $sanitizer->entities1($name) ?>" <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> class="field-input txtBox txtBox_multi <?= $error ? 'txtBox_error' : '' ?>" placeholder="<?= $sanitizer->entities1($placeholder) ?>" <?= $disabled ? 'disabled' : '' ?>><?= $sanitizer->entities1($value) ?></textarea>
	<?php endif ?>

<?php include('../Field/footer.php') ?>