<div class="field checkboxesField">

	<?php if($bulkSelectOptions): ?>
		<div class="checkboxesField-bulkSelect right">
			<button type="button" class="checkboxesField-all"><i class="fa fa-check-square-o"></i> All</button><button type="button" class="checkboxesField-none"> None</button>
		</div>
	<?php endif ?>

	<label class="field-label">
		<?php if($icon): ?><i class="field-icon fa fa-<?= $icon ?>"></i><?php endif ?>
		<?= $label ?>
		<?php if($required): ?><span class="field-required">*</span><?php endif ?>
	</label>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

	<input type="hidden" class="field-fallback" name="<?= $name ?>" value="[#]" />

	<ul class="checkboxesField-list <?= $columnize ? 'checkboxesField-columnize' : '' ?>">
		<?php foreach($options as $option): ?>
			<li class="<?= $columnize ? 'dontsplit' : '' ?>">
				<label class="checkboxesField-label"><input type="checkbox" name="<?= $name ?>[]" value="<?= $sanitizer->entities($option['value']) ?>" <?= $value && in_array($option['value'], $value) ? 'checked="checked"' : '' ?> <?= $disabled ? 'disabled' : '' ?> /> <?= $option['label'] ?></label>
			</li>
		<?php endforeach ?>
	</ul>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>