<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($value): ?>
				<?= $sanitizer->entities($value) ?>
			<?php else: ?>
				<span class="field-noValue"><i class="fa fa-minus-circle"></i> No Value</span>
			<?php endif ?>
		</div>
	<?php else: ?>
		<div class="timeField-outer">
			<input maxlength="<?= $maxLength ?>" size="<?= $maxLength ?>" name="<?= $name ?>" id="<?= $name ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $placeholder ?>" value="<?= $sanitizer->entities($value) ?>" data-twentyfourhour="<?= $twentyFourHour ?>" data-ampm="<?= $amPm ?>" <?= $disabled ? 'disabled' : '' ?> autocomplete="off" />

			<?php if(!$hideIncrementButtons): ?>
				<button type="button" class="timeField-down" tabindex="-1"><i class="fa fa-caret-down"></i></button>
				<button type="button" class="timeField-up" tabindex="-1"><i class="fa fa-caret-up"></i></button>
			<?php endif ?>
		</div>
	<?php endif ?>

<?php include('../Field/footer.php') ?>