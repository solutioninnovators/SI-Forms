<div data-field-name="<?= $sanitizer->entities1($name) ?>"
class="field <?= $sanitizer->entities1($cssClass) ?>"
<?= $ajaxValidate ? 'data-ajax-validate="1"' : '' ?>
<?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>
<?php if(count($dependsOn)): ?>
	data-depends-on="<?= $sanitizer->entities1(implode(' ', $dependsOn)) ?>"
<?php endif ?>
<?php foreach($extraAttributes as $attr => $val): ?>
	<?= $sanitizer->entities1($attr) ?>="<?= $sanitizer->entities1($val) ?>"
<?php endforeach?>
>

	<?php if($showLabel && $label): ?>
		<label class="field-label" for="input_<?= $sanitizer->entities1($id) ?>">
			<?php if($icon): ?><i class="field-icon fa fa-fw fa-<?= $sanitizer->entities1($icon) ?>"></i><?php endif ?>
			<?= $sanitizer->entities1($label) ?>
			<?php if($required): ?><span class="field-required">*</span><?php endif ?>
		</label>
	<?php endif ?>

	<div class="field-saveBadge saveBadge"></div>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>