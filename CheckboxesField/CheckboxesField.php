<?php include('../Field/header.php') ?>

	<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="[#]" <?= $disabled ? 'disabled' : '' ?> />

	<?php if($readOnly): ?>

		<div class="field-readOnly">
			<?php foreach($value as $val): ?>
				<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>[]" value="<?= $sanitizer->entities1($val) ?>" <?= $disabled ? 'disabled' : '' ?> />
			<?php endforeach ?>

			<?php if($selectedLabel): ?>
				<?= $sanitizer->entities1($selectedLabel) ?>
			<?php else:	?>
				<div class="field-noValue"><i class="fa fa-minus-circle"></i> Not Selected</div>
			<?php endif ?>
		</div>

	<?php else: ?>

		<?php if($bulkSelectOptions): ?>
			<div class="checkboxesField-bulkSelect">
				<button type="button" class="checkboxesField-all"><i class="fa fa-check-square-o"></i> All</button><button type="button" class="checkboxesField-none"> None</button>
			</div>
		<?php endif ?>

		<ul class="checkboxesField-list <?= $columnize ? 'checkboxesField-columnize' : '' ?>">
			<?php foreach($options as $option): ?>
				<li class="<?= $columnize ? 'dontsplit' : '' ?>">
					<label class="checkboxesField-label"><input <?= $formAttribute ?> type="checkbox" name="<?= $sanitizer->entities1($name) ?>[]" value="<?= $sanitizer->entities1($option['value']) ?>" <?= $value && in_array($option['value'], $value) ? 'checked="checked"' : '' ?> <?= $disabled ? 'disabled' : '' ?> /> <?= $sanitizer->entities1($option['label']) ?></label>
				</li>
			<?php endforeach ?>
		</ul>

	<?php endif ?>

<?php include('../Field/footer.php') ?>