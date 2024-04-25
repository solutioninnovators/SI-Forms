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
		<div class="autocompleteField-inputs">
			<div class="autocompleteField-spinner"><i class="fa fa-spin fa-circle-o-notch"></i></div>

			<input
				<?= $formAttribute ?>
				maxlength="<?= $sanitizer->entities1($maxLength) ?>"
				<?= $id ? 'id="input_'.$sanitizer->entities1($id).'"' : '' ?>
				class="field-input txtBox<?= $error ? ' txtBox_error' : '' ?> <?= $sanitizer->entities1($inputClasses) ?>"
				type="text"
				placeholder="<?= $sanitizer->entities1($placeholder) ?>"
				value="<?= $sanitizer->entities1($displayValue) ?>"
				<?php if(count($settings)): ?>data-settings="<?= $sanitizer->entities1(json_encode($settings)) ?>"<?php endif ?>
				<?= $disabled ? 'disabled' : '' ?>
			/>

			<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>" class="autocompleteField-value" value="<?= $sanitizer->entities1($value) ?>" <?= $disabled ? 'disabled' : '' ?> />
		</div>
	<?php endif ?>

<?php include('../Field/footer.php') ?>