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
		<div class="numberField-outer">
			<input maxlength="<?= $maxLength ?>" size="<?= $maxLength ?>" name="<?= $name ?>" id="<?= $name ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="text" placeholder="<?= $placeholder ?>" value="<?= $sanitizer->entities($value) ?>" data-number-min="<?= $min ?>" data-number-max="<?= $max ?>" />

			<button type="button" class="numberField-down"><i class="fa fa-caret-down"></i></button>
			<button type="button" class="numberField-up"><i class="fa fa-caret-up"></i></button>
		</div>
	<?php endif ?>

<?php include('../Field/footer.php') ?>