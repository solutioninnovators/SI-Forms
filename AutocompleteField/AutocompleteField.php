<?php include('../Field/header.php') ?>

	<div class="autocompleteField-inputs">
		<i class="autocompleteField-spinner fa fa-spin fa-circle-o-notch hide"></i>

		<input maxlength="<?= $sanitizer->entities1($maxLength) ?>" id="input_<?= $sanitizer->entities1($id) ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $sanitizer->entities1($placeholder) ?>" value="<?= $sanitizer->entities1($displayValue) ?>" <?php if(count($settings)): ?>data-settings="<?= $sanitizer->entities1(json_encode($settings)) ?>"<?php endif ?> <?= $disabled ? 'disabled' : '' ?> />

		<input type="hidden" name="<?= $sanitizer->entities1($name) ?>" class="autocompleteField-value" value="<?= $sanitizer->entities1($value) ?>" />
	</div>

<?php include('../Field/footer.php') ?>