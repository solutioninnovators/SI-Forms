<div class="field field_date">

	<label class="field-label" for="<?= $name ?>">
		<i class="field-icon fa fa-<?= $icon ?>"></i> <?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<?php if($readOnly): ?>
		<?php if($value): ?>
			<?= $sanitizer->entities($value) ?>
		<?php else: ?>
			<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
		<?php endif ?>
	<?php else: ?>
		<input maxlength="<?= $maxLength ?>" name="<?= $name ?>" id="<?= $name ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $placeholder ?>" value="<?= $sanitizer->entities($value) ?>" <?= $disabled ? 'disabled' : '' ?> />
	<?php endif ?>

	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>