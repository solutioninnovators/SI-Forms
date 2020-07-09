<?php include('../Field/header.php') ?>

	<div class="autocompleteField-inputs">
		<i class="autocompleteField-spinner fa fa-spin fa-circle-o-notch hide"></i>

		<input maxlength="<?= $maxLength ?>" id="<?= $id ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $placeholder ?>" value="<?= $sanitizer->entities($displayValue) ?>" <?= $disabled ? 'disabled' : '' ?> />

		<input type="hidden" name="<?= $name ?>" class="autocompleteField-value" value="<?= $sanitizer->entities($value) ?>" />
	</div>

<?php include('../Field/footer.php') ?>