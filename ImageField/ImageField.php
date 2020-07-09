<?php include('../Field/header.php') ?>

	<?php if($imgPreview && $savePage->{$saveField}): ?>
		<img class="imageField-preview" src="<?= $savePage->{$saveField}->size(200,200)->url ?>" />
	<?php endif ?>

	<div class="field-input imageField-input">
		<input class="<?= $sanitizer->entities1($buttonClasses) ?>" type="file" name="<?= $sanitizer->entities1($name) ?>" id="input_<?= $sanitizer->entities1($id) ?>" />
	</div>

<?php include('../Field/footer.php') ?>