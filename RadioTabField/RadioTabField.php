<?php include('../Field/header.php') ?>
	
	<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="#" <?= $disabled ? 'disabled' : '' ?> />
	
	<?php foreach($options as $option): ?>
		<input <?= $formAttribute ?> type="radio" name="<?= $sanitizer->entities1($name) ?>" id="radioTab_<?= $sanitizer->entities1($option['value']) ?>" class="radioTab-input" value="<?= $sanitizer->entities1($option['value']) ?>" <?= $option['value'] == $value ? 'checked' : '' ?> /><label class="radioTab-label radioTab_<?= $sanitizer->entities1($option['value']) ?>-label tabs-tab tab <?= isset($option['tooltip']) ? 'tooltip' : '' ?>" title="<?= isset($option['tooltip']) ? $sanitizer->entities1($option['tooltip']) : '' ?>" for="radioTab_<?= $sanitizer->entities1($option['value']) ?>"><?= $option['label'] ?></label>&nbsp;
	<?php endforeach ?>


<?php include('../Field/footer.php') ?>