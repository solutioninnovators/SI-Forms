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
		<?= $formAttribute ?>
		type="<?= $sanitizer->entities1($type) ?>"
		maxlength="<?= $sanitizer->entities1($maxLength) ?>"
		name="<?= $sanitizer->entities1($name) ?>"
		<?= $id ? 'id="input_'.$sanitizer->entities1($id).'"' : '' ?>
		class="field-input txtBox<?= $error ? ' txtBox_error' : '' ?> <?= $sanitizer->entities1($inputClasses) ?>"
		placeholder="<?= $sanitizer->entities1($placeholder) ?>"
		value="<?= $this->type !== 'password' ? $sanitizer->entities1($value) : '' ?>"
		<?= $disabled ? 'disabled="disabled"' : '' ?>
		<?= $autofocus ? 'autofocus="autofocus"' : '' ?>
		<?= !$autocomplete ? 'autocomplete="off"' : '' ?>
		<?= $attributeString ?>
	/>
<?php endif ?>

<?php include('../Field/footer.php') ?>