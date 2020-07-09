<?php include('../Field/header.php') ?>

	<input type="hidden" class="field-fallback" name="<?= $name ?>" value="[#]" />

	<select class="multipleSelectField-select" multiple="multiple" name="<?= $name ?>[]">
		<?php foreach($options as $option): ?>
			<option value="<?= $option['value'] ?>" <?= in_array($option['value'], $value) ? 'selected="selected"' : '' ?>><?= $option['label'] ?></option>
		<?php endforeach ?>
	</select>

<?php include('../Field/footer.php') ?>