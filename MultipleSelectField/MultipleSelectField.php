<div class="field multipleSelectField" <?php foreach($extraAttributes as $attr => $val): ?><?= $sanitizer->entities($attr) ?>="<?= $sanitizer->entities($val) ?>" <? endforeach?>>

	<label class="field-label">
		<?php if($icon): ?><i class="field-icon fa fa-<?= $icon ?>"></i><?php endif ?>
		<?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<input type="hidden" class="field-fallback" name="<?= $name ?>" value="[#]" />

	<select class="multipleSelectField-select" multiple="multiple" name="<?= $name ?>[]">
		<?php foreach($options as $option): ?>
			<option value="<?= $option['value'] ?>" <?= in_array($option['value'], $value) ? 'selected="selected"' : '' ?>><?= $option['label'] ?></option>
		<?php endforeach ?>
	</select>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>