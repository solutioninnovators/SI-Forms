<?php include('../Field/header.php') ?>

	<?php if($bulkSelectOptions): ?>
		<div class="checkboxesField-bulkSelect">
			<button type="button" class="checkboxesField-all"><i class="fa fa-check-square-o"></i> All</button><button type="button" class="checkboxesField-none"> None</button>
		</div>
	<?php endif ?>

	<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" <?= $disabled ? 'disabled' : '' ?> />

	<ul class="checkboxesField-list <?= $columnize ? 'checkboxesField-columnize' : '' ?>">
		<?php foreach($options as $option): ?>
			<li class="<?= $columnize ? 'dontsplit' : '' ?>">
				<label class="checkboxesField-label"><input <?= $formAttribute ?> type="checkbox" name="<?= $sanitizer->entities1($name) ?>[]" value="<?= $sanitizer->entities1($option['value']) ?>" <?= $value && in_array($option['value'], $value) ? 'checked="checked"' : '' ?> <?= $disabled ? 'disabled' : '' ?> /> <?= $sanitizer->entities1($option['label']) ?></label>
			</li>
		<?php endforeach ?>
	</ul>

<?php include('../Field/footer.php') ?>