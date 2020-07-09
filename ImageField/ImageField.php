<?php include('../Field/header.php') ?>

	<?php if($imgPreview && $savePage->{$saveField}): ?>
		<img class="imageField-preview" src="<?= $savePage->{$saveField}->size(200,200)->url ?>" />
	<?php endif ?>

	<div class="field-input imageField-input">
		<input class="<?= $buttonClasses ?>" type="file" name="<?= $name ?>"  />
	</div>

<?php include('../Field/footer.php') ?>