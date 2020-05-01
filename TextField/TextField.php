<?php include('../Field/header.php') ?>

<?php if($readOnly): ?>
	<div class="field-readOnly">
		<?php if($value): ?>
			<?= $sanitizer->entities($value) ?>
		<?php else: ?>
			<div class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</div>
		<?php endif ?>
	</div>
<?php else: ?>
	<input maxlength="<?= $maxLength ?>" name="<?= $name ?>" <?= $attributeString ?> id="<?= $id ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="<?= $type ?>" placeholder="<?= $placeholder ?>" value="<?= $this->type !== 'password' ? $sanitizer->entities($value) : '' ?>" <?= $disabled ? 'disabled="disabled"' : '' ?> <?= $autofocus ? 'autofocus="autofocus"' : '' ?> <?= !$autocomplete ? 'autocomplete="off"' : '' ?> />
<?php endif ?>

<?php include('../Field/footer.php') ?>