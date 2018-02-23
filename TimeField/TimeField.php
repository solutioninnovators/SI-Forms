<div class="field timeField">
	<?php if($label): ?>
		<label class="field-label" for="<?= $name ?>">
			<i class="field-icon fa fa-<?= $icon ?>"></i> <?= $label ?>
			<?php if($required): ?><span class="field-required">*</span><?php endif ?>
		</label>
	<?php endif ?>

	<?php if($description): ?>
		<p class="field-description"><?= $description ?></p>
	<?php endif ?>

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
			<input maxlength="<?= $maxLength ?>" size="<?= $maxLength ?>" name="<?= $name ?>" id="<?= $name ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $placeholder ?>" value="<?= $sanitizer->entities($value) ?>" data-twentyfourhour="<?= $twentyFourHour ?>" data-ampm="<?= $amPm ?>" <?= $disabled ? 'disabled' : '' ?> />

			<?php if(!$hideIncrementButtons): ?>
				<button type="button" class="timeField-down"><i class="fa fa-caret-down"></i></button>
				<button type="button" class="timeField-up"><i class="fa fa-caret-up"></i></button>
			<?php endif ?>
		</div>
	<?php endif ?>

	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<?php if($error): ?>
		<span class="field-error"><i class="fa fa-caret-up"></i> <?= $error ?></span>
	<?php endif ?>

</div>