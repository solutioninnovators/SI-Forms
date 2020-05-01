<div class="field autocompleteField">

	<label class="field-label" for="<?= $id ?>">
		<?php if($icon): ?><i class="field-icon fa fa-<?= $icon ?>"></i><?php endif ?>
		<?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<div class="autocompleteField-inputs">
		<i class="autocompleteField-spinner fa fa-spin fa-circle-o-notch hide"></i>

		<input maxlength="<?= $maxLength ?>" id="<?= $id ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $placeholder ?>" value="<?= $sanitizer->entities($displayValue) ?>" <?= $disabled ? 'disabled' : '' ?> />

		<input type="hidden" name="<?= $name ?>" class="autocompleteField-value" value="<?= $sanitizer->entities($value) ?>" />
	</div>

	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>