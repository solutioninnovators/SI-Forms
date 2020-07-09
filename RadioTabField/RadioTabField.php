<?php include('../Field/header.php') ?>
	
	<input type="hidden" class="field-fallback" name="<?= $name ?>" value="#" />
	
	<?php foreach($options as $option): ?>
		<input type="radio" name="<?= $name ?>" id="radioTab_<?= $option['value'] ?>" class="radioTab-input" value="<?= $option['value'] ?>" <?= $option['value'] == $value ? 'checked' : '' ?> /><label class="radioTab-label radioTab_<?= $option['value'] ?>-label tabs-tab tab <?= isset($option['tooltip']) ? 'tooltip' : '' ?>" title="<?= isset($option['tooltip']) ? $option['tooltip'] : '' ?>" for="radioTab_<?= $option['value'] ?>"><?= $option['label'] ?></label>&nbsp;
	<?php endforeach ?>


<?php include('../Field/footer.php') ?>