<?php include('../Field/header.php') ?>

<?php if($readOnly): ?>
	<div class="field-readOnly">
		<?php if($value): ?>
			<?= $sanitizer->entities1($value) ?>
		<?php else: ?>
			<div class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</div>
		<?php endif ?>
	</div>
<?php else: ?>
	<input <?= $formAttribute ?> maxlength="<?= $sanitizer->entities1($maxLength) ?>" name="<?= $sanitizer->entities1($name) ?>" <?= $attributeString ?> <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="<?= $sanitizer->entities1($type) ?>" placeholder="<?= $sanitizer->entities1($placeholder) ?>" value="<?= $this->type !== 'password' ? $sanitizer->entities1($value) : '' ?>" <?= $disabled ? 'disabled="disabled"' : '' ?> <?= $autofocus ? 'autofocus="autofocus"' : '' ?> <?= !$autocomplete ? 'autocomplete="off"' : '' ?> />
<?php endif ?>

<?php include('../Field/footer.php') ?>