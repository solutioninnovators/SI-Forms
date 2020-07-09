<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<?php if($selectedLabel): ?>
				<?= $selectedLabel ?>
			<?php else:	?>
				<div class="field-noValue"><i class="fa fa-minus-circle"></i> Not Selected</div>
			<?php endif ?>
		</div>
	<?php else: ?>
		<select id="<?= $id ?>" name="<?= $name ?>" class="field-input txtBox txtBox_select <?= $autocomplete ? 'selectField-autocomplete' : '' ?>" <?= $disabled ? 'disabled' : '' ?>>
			<?php if($placeholder): ?>
				<option value=""><?= $placeholder ?></option>
			<?php endif ?>

			<?php foreach($options as $option): ?>
				<option value="<?= $sanitizer->entities($option['value']) ?>" <?= $value == $option['value'] ? 'selected="selected"' : '' ?>><?= $option['label'] ?></option>
			<?php endforeach ?>
		</select>
	<?php endif ?>

<?php include('../Field/footer.php') ?>