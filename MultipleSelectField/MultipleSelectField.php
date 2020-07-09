<?php include('../Field/header.php') ?>

	<input type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" />

	<select class="multipleSelectField-select" multiple="multiple" name="<?= $sanitizer->entities1($name) ?>[]" id="input_<?= $sanitizer->entities1($id) ?>">
		<?php foreach($options as $option): ?>
			<option value="<?= $sanitizer->entities1($option['value']) ?>" <?= in_array($option['value'], $value) ? 'selected="selected"' : '' ?>><?= $sanitizer->entities1($option['label']) ?></option>
		<?php endforeach ?>
	</select>

<?php include('../Field/footer.php') ?>