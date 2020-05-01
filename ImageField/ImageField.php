<div class="field imageField">
	<label class="field-label" for="<?= $name ?>">
		<i class="field-icon fa fa-fw fa-<?= $icon ?>"></i> <?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

    <?php if($description): ?>
        <p class="field-description"><?= $description ?></p>
    <?php endif ?>

	<?php if($imgPreview && $savePage->{$saveField}): ?>
		<img class="imageField-preview" src="<?= $savePage->{$saveField}->size(200,200)->url ?>" />
	<?php endif ?>

	<div class="field-input imageField-input">
		<input class="<?= $buttonClasses ?>" type="file" name="<?= $name ?>"  />
	</div>

	<?php if($notes): ?>
		<p class="field-notes"><?= $notes ?></p>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>
</div>
