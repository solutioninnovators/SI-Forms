<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>" value="<?= $sanitizer->entities1($value) ?>" <?= $disabled ? 'disabled' : '' ?> />
			<?php if($value): ?>
				<i class="fa fa-lock"></i> <?= $sanitizer->entities1($selectedOption['label']) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<input <?= $formAttribute ?> type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="#" <?= $disabled ? 'disabled' : '' ?> />

		<?php foreach($options as $option): ?>
			<label class="radioField-option <?= isset($option['tooltip']) ? 'tooltip' : '' ?> <?= isset($option['disabled']) && $option['disabled'] ? 'radioField-disabled':'' ?>" title="<?= isset($option['tooltip']) ? $option['tooltip'] : '' ?>"  >
				<input <?= $formAttribute ?> type="radio" name="<?= $name ?>" value="<?= $option['value'] ?>" <?= $option['value'] == $value ? 'checked' : '' ?> <?= $disabled || (isset($option['disabled']) && $option['disabled']) ? 'disabled' : '' ?> />
				<?= $option['label'] ?>
				<?php if(isset($option['notes'])): ?>
					<p class="field-option-notes"><?= $option['notes'] ?></p>
				<?php endif ?>
			</label>
		<?php endforeach ?>
	<?php endif ?>

<?php include('../Field/footer.php') ?>