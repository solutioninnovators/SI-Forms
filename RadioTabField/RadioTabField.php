<div class="field radioTab field_<?= $name ?> tabs">

	<label class="field-label" for="<?= $name ?>">
		<?php if($icon): ?><i class="field-icon fa fa-<?= $icon ?>"></i><?php endif ?>
		<?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<?php foreach($options as $option): ?>
		<input type="radio" name="<?= $name ?>" id="radioTab_<?= $option['value'] ?>" class="radioTab-input" value="<?= $option['value'] ?>" <?= $option['value'] == $value ? 'checked' : '' ?> /><label class="radioTab-label radioTab_<?= $option['value'] ?>-label tabs-tab tab <?= isset($option['tooltip']) ? 'tooltip' : '' ?>" title="<?= isset($option['tooltip']) ? $option['tooltip'] : '' ?>" for="radioTab_<?= $option['value'] ?>"><?= $option['label'] ?></label>&nbsp;
	<?php endforeach ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>