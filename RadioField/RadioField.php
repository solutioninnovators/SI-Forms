<div class="field radioField field_<?= $name ?> tabs">

	<?php if($label): ?>
		<label class="field-label" for="<?= $name ?>">
			<?php if($icon): ?><i class="field-icon fa fa-<?= $icon ?>"></i><?php endif ?>
			<?= $label ?>
			<?php if($required): ?><span class="field-required">*</span><?php endif ?>
		</label>
	<?php endif ?>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<?php foreach($options as $option): ?>
		<label class="radioField-option <?= isset($option['tooltip']) ? 'tooltip' : '' ?>" title="<?= isset($option['tooltip']) ? $option['tooltip'] : '' ?>">
			<input type="radio" name="<?= $name ?>" value="<?= $option['value'] ?>" <?= $option['value'] == $value ? 'checked' : '' ?> />
			<?= $option['label'] ?>
		</label>
	<?php endforeach ?>
	
	<?php if($notes): ?>
		<p class="field-notes"><?= $notes ?></p>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>