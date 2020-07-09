<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($value): ?>
				<?= $sanitizer->entities($value) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<textarea rows="<?= $rows ?>" maxlength="<?= $maxLength ?>" name="<?= $name ?>" id="<?= $id ?>" class="field-input txtBox txtBox_multi <?= $error ? 'txtBox_error' : '' ?>" placeholder="<?= $placeholder ?>" <?= $disabled ? 'disabled' : '' ?>><?= $sanitizer->entities($value) ?></textarea>
	<?php endif ?>

<?php include('../Field/footer.php') ?>