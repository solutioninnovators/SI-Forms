<?php include('../Field/header.php') ?>

	<?php if($readOnly): ?>
		<div class="field-readOnly">
			<input <?= $formAttribute ?> type="hidden" name="<?= $sanitizer->entities1($name) ?>" value="<?= $sanitizer->entities1($value) ?>" <?= $disabled ? 'disabled' : '' ?> />
			<?php if($selectedLabel): ?>
				<?= $sanitizer->entities1($selectedLabel) ?>
			<?php else:	?>
				<div class="field-noValue"><i class="fa fa-minus-circle"></i> Not Selected</div>
			<?php endif ?>
		</div>
	<?php else: ?>
		<select
			<?= $formAttribute ?>
			<?= $id ? 'id="input_'.$sanitizer->entities1($id).'"' : '' ?>
			name="<?= $sanitizer->entities1($name) ?>"
			class="field-input txtBox txtBox_select<?= $autocomplete ? ' selectField-autocomplete' : '' ?> <?= $sanitizer->entities1($inputClasses) ?>"
			<?= $disabled ? 'disabled' : '' ?>
			<?= $autofocus ? 'autofocus="autofocus"' : '' ?>
			<?= !$autocomplete ? 'autocomplete="off"' : '' ?>
		>
			<?php if($placeholder): ?>
				<option value=""><?= $sanitizer->entities1($placeholder) ?></option>
			<?php endif ?>

			<?php foreach($options as $option): ?>
				<option
                    value="<?= $sanitizer->entities1($option['value']) ?>"
                    <?= $value == $option['value'] ? 'selected="selected"' : '' ?>
                    <?php if(isset($option['data'])): ?>
                    <?php foreach($option['data'] as $key => $v): ?>
                        data-<?= $key ?>="<?= $v ?>"
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?= !empty($option['disabled']) ? 'disabled' : '' ?>>
                    <?= $sanitizer->entities1($option['label']) ?>
                </option>
			<?php endforeach ?>
		</select>
	<?php endif ?>

<?php include('../Field/footer.php') ?>