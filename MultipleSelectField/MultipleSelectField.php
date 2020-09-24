<?php include('../Field/header.php') ?>

	<input type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" <?= $disabled ? 'disabled' : '' ?> />

	<select class="field-input multipleSelectField-select" multiple="multiple" name="<?= $sanitizer->entities1($name) ?>[]" <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> <?= $disabled ? 'disabled' : '' ?>>
		<?php foreach($options as $option): ?>
			<option value="<?= $sanitizer->entities1($option['value']) ?>" <?= in_array($option['value'], $value) ? 'selected="selected"' : '' ?>><?= $sanitizer->entities1($option['label']) ?></option>
		<?php endforeach ?>
	</select>

<?php include('../Field/footer.php') ?>