<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>" value="<?= $sanitizer->entities1($value) ?>" <?= $disabled ? 'disabled' : '' ?> />
			<?php if($value): ?>
				<?= $sanitizer->entities1($value) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<div class="numberField-outer">
			<input
				<?= $formAttribute ?>
				type="text"
				maxlength="<?= $sanitizer->entities1($maxLength) ?>"
				size="<?= $sanitizer->entities1($maxLength) ?>"
				name="<?= $sanitizer->entities1($name) ?>"
				<?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?>
				class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?> <?= $inputClasses ?>"
				placeholder="<?= $sanitizer->entities1($placeholder) ?>"
				value="<?= $sanitizer->entities1($value) ?>"
				data-number-min="<?= $sanitizer->entities1($min) ?>"
				data-number-max="<?= $sanitizer->entities1($max) ?>"
				data-number-step="<?= $sanitizer->entities1($step) ?>"
				<?= $disabled ? 'disabled' : '' ?>
			/>

            <?php if(!$removeButtons): ?>
                <button type="button" class="numberField-down"><i class="fa fa-caret-down"></i></button>
                <button type="button" class="numberField-up"><i class="fa fa-caret-up"></i></button>
            <?php endif ?>
		</div>
	<?php endif ?>

<?php include('../Field/footer.php') ?>