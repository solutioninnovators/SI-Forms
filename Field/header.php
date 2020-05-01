<div class="field <?= $cssClass ?>" <?= $ajaxValidate ? 'data-ajax-validate="1"' : '' ?> <?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>>

	<?php if($label): ?>
		<label class="field-label" for="<?= $id ?>">
			<?php if($icon): ?><i class="field-icon fa fa-fw fa-<?= $icon ?>"></i><?php endif ?>
			<?= $label ?>
			<?php if($required): ?><span class="field-required">*</span><?php endif ?>
		</label>
	<?php endif ?>

	<div class="field-saveBadge saveBadge"></div>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>