<?php include('../Field/header.php') ?>

	<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="0" <?= $disabled ? 'disabled' : '' ?> />

	<?php if($readOnly): ?>

		<div class="field-readOnly">
			<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>" value="<?= $sanitizer->entities1($value) ?>" <?= $disabled ? 'disabled' : '' ?> />

			<?php if($value): ?>
				<i class="fa fa-check-square-o"></i>
			<?php else: ?>
				<i class="fa fa-square-o"></i>
			<?php endif ?>
		</div>

	<?php else: ?>

		<label class="checkboxField-label <?= $toggleSwitch ? 'toggleSwitch' : '' ?>">
				<input
					<?= $formAttribute ?>
					type="checkbox"
					class="field-input <?= $sanitizer->entities1($inputClasses) ?>"
					name="<?= $sanitizer->entities1($name) ?>"
					<?= $id ? 'id="input_'.$sanitizer->entities1($id).'"' : '' ?>
					value="1"
					<?= $value == 1 ? 'checked="checked"' : '' ?>
					<?= $disabled ? 'disabled' : '' ?>
				/>
			<?php if($toggleSwitch): ?>
				<span class="toggleSwitch-slider"></span>
			<?php endif ?>
			<?= $sanitizer->entities1($label) ?>
		</label>

	<?php endif ?>

<?php include('../Field/footer.php') ?>