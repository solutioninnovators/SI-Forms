<div class="field textField" <?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>>

	<?php if($label): ?>
		<label class="field-label" for="<?= $id ?>">
			<?php if($icon): ?><i class="field-icon fa fa-fw fa-<?= $icon ?>"></i><?php endif ?>
			<?= $label ?>
			<?php if($required): ?><span class="field-required">*</span><?php endif ?>
		</label>
	<?php endif ?>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($value): ?>
				<?= $sanitizer->entities($value) ?>
			<?php else: ?>
				<div class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</div>
			<?php endif ?>
		</div>
	<?php else: ?>
		<input maxlength="<?= $maxLength ?>" name="<?= $name ?>" <?= $attributeString ?> id="<?= $id ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="<?= $type ?>" placeholder="<?= $placeholder ?>" value="<?= $this->type !== 'password' ? $sanitizer->entities($value) : '' ?>" <?= $disabled ? 'disabled="disabled"' : '' ?> <?= $autofocus ? 'autofocus="autofocus"' : '' ?> />
	<?php endif ?>

	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>