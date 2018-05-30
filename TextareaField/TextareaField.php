<div class="field textareaField" <?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>>

	<label class="field-label" for="<?= $name ?>">
		<i class="field-icon fa fa-<?= $icon ?>"></i> <?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<div class="field-saveBadge saveBadge"></div>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($value): ?>
				<?= $sanitizer->entities($value) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<textarea rows="<?= $rows ?>" maxlength="<?= $maxLength ?>" name="<?= $name ?>" id="<?= $name ?>" class="field-input txtBox txtBox_multi <?= $error ? 'txtBox_error' : '' ?>" placeholder="<?= $placeholder ?>" <?= $disabled ? 'disabled' : '' ?>><?= $sanitizer->entities($value) ?></textarea>
	<?php endif ?>
	
	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<span class="field-error <?= !$error ? 'hide' : '' ?>"><i class="fa fa-caret-up"></i> <span class="field-errorTxt"><?= $error ?></span></span>

</div>