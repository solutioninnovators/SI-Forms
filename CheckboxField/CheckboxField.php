<?php include('../Field/header.php') ?>

	<input type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="0" <?= $disabled ? 'disabled' : '' ?> />

	<label class="checkboxField-label <?= $toggleSwitch ? 'toggleSwitch' : '' ?>">
		<input type="checkbox" name="<?= $sanitizer->entities1($name) ?>" id="input_<?= $sanitizer->entities1($id) ?>" value="1" <?= $value == 1 ? 'checked="checked"' : '' ?> />
		<?php if($toggleSwitch): ?>
			<span class="toggleSwitch-slider"></span>
		<?php endif ?>
		<?= $sanitizer->entities1($label) ?>
	</label>

<?php include('../Field/footer.php') ?>