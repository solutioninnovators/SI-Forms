<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($value): ?>
				<?= $sanitizer->entities1($value) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<div class="timeField-outer">
			<input maxlength="<?= $sanitizer->entities1($maxLength) ?>" size="<?= $sanitizer->entities1($maxLength) ?>" name="<?= $sanitizer->entities1($name) ?>" <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $sanitizer->entities1($placeholder) ?>" value="<?= $sanitizer->entities1($value) ?>" data-twentyfourhour="<?= $sanitizer->entities1($twentyFourHour) ?>" data-ampm="<?= $sanitizer->entities1($amPm) ?>" <?= $disabled ? 'disabled' : '' ?> autocomplete="off" />

			<?php if(!$hideIncrementButtons): ?>
				<button type="button" class="timeField-down" tabindex="-1"><i class="fa fa-caret-down"></i></button>
				<button type="button" class="timeField-up" tabindex="-1"><i class="fa fa-caret-up"></i></button>
			<?php endif ?>
		</div>
	<?php endif ?>

<?php include('../Field/footer.php') ?>