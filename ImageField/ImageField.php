<?php include('../Field/header.php') ?>

	<?php if($imgPreview && $savePage->{$saveField}): ?>
		<img class="imageField-preview" src="<?= $savePage->{$saveField}->size(200,200)->url ?>" />
	<?php else: ?>
		<div class="field-noValue"><i class="fa fa-minus-circle"></i> No Image</div>
	<?php endif ?>

	<?php if(!$readOnly): ?>
		<div class="field-input imageField-input">
			<input type="file" class="<?= $sanitizer->entities1($buttonClasses) ?>" name="<?= $sanitizer->entities1($name) ?>" id="input_<?= $sanitizer->entities1($id) ?>" />
		</div>
	<?php endif ?>

<?php include('../Field/footer.php') ?>