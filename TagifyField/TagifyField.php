<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>" value="<?= $sanitizer->entities1($value) ?>" <?= $disabled ? 'disabled' : '' ?> />
			<?php if($value): ?>
				<?= $sanitizer->entities1($value) ?>
			<?php else: ?>
				<div class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</div>
			<?php endif ?>
		</div>
	<?php else: ?>
		<input
				type="text"
				<?= $formAttribute ?>
				maxlength="<?= $sanitizer->entities1($maxLength) ?>"
				<?php if($id): ?>
					id="input_<?= $sanitizer->entities1($id) ?>"
				<?php endif ?>
				class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>"
				placeholder="<?= $sanitizer->entities1($placeholder) ?>"
				value="<?= $sanitizer->entities1($displayValue) ?>"
				<?php if(count($settings)): ?>
					data-settings="<?= $sanitizer->entities1(json_encode($settings)) ?>"
				<?php endif ?>
				<?= $disabled ? 'disabled' : '' ?>
		/>
	<?php endif ?>

<?php include('../Field/footer.php') ?>