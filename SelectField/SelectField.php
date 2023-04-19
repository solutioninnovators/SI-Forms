<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($selectedLabel): ?>
				<?= $sanitizer->entities1($selectedLabel) ?>
			<?php else:	?>
				<div class="field-noValue"><i class="fa fa-minus-circle"></i> Not Selected</div>
			<?php endif ?>
		</div>
	<?php else: ?>
		<select <?= $formAttribute ?> <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> name="<?= $sanitizer->entities1($name) ?>" class="field-input txtBox txtBox_select <?= $autocomplete ? 'selectField-autocomplete' : '' ?>" <?= $disabled ? 'disabled' : '' ?>>
			<?php if($placeholder): ?>
				<option value=""><?= $sanitizer->entities1($placeholder) ?></option>
			<?php endif ?>

			<?php foreach($options as $option): ?>
				<option value="<?= $sanitizer->entities1($option['value']) ?>" <?= $value == $option['value'] ? 'selected="selected"' : '' ?> <?= !empty($option['disabled']) ? 'disabled' : '' ?>><?= $sanitizer->entities1($option['label']) ?></option>
			<?php endforeach ?>
		</select>
	<?php endif ?>

<?php include('../Field/footer.php') ?>