<?php include('../Field/header.php') ?>

	<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="0" <?= $disabled ? 'disabled' : '' ?> />

	<label class="checkboxField-label <?= $toggleSwitch ? 'toggleSwitch' : '' ?>">
		<input class="field-input" <?= $formAttribute ?> type="checkbox" name="<?= $sanitizer->entities1($name) ?>" <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> value="1" <?= $value == 1 ? 'checked="checked"' : '' ?> <?= $disabled ? 'disabled' : '' ?> />
		<?php if($toggleSwitch): ?>
			<span class="toggleSwitch-slider"></span>
		<?php endif ?>
		<?= $sanitizer->entities1($label) ?>
	</label>

<?php include('../Field/footer.php') ?>