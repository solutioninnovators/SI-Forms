<?php include('../Field/header.php') ?>

	<input type="hidden" class="field-fallback" name="<?= $sanitizer->entities1($name) ?>" value="#" />

	<?php foreach($options as $option): ?>
		<label class="radioField-option <?= isset($option['tooltip']) ? 'tooltip' : '' ?> <?= isset($option['disabled']) && $option['disabled'] ? 'radioField-disabled':'' ?>" title="<?= isset($option['tooltip']) ? $option['tooltip'] : '' ?>"  >
			<input type="radio" name="<?= $name ?>" value="<?= $option['value'] ?>" <?= $option['value'] == $value ? 'checked' : '' ?> <?= isset($option['disabled']) && $option['disabled'] ? 'disabled':'' ?>  />
			<?= $option['label'] ?>
            <?php if(isset($option['notes'])): ?>
                <p class="field-option-notes"><?= $option['notes'] ?></p>
            <?php endif ?>
		</label>
	<?php endforeach ?>

<?php include('../Field/footer.php') ?>