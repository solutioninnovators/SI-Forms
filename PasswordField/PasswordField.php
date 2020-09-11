<div data-field-name="<?= $sanitizer->entities1($name) ?>"
class="field <?= $sanitizer->entities1($cssClass) ?>"
<?= $ajaxValidate ? 'data-ajax-validate="1"' : '' ?>
<?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>
<?php if(count($dependsOn)): ?>
	data-depends-on="<?= $sanitizer->entities1(implode(' ', $dependsOn)) ?>"
<?php endif ?>
<?php foreach($extraAttributes as $attr => $val): ?>
	<?= $sanitizer->entities1($attr) ?>="<?= $sanitizer->entities1($val) ?>"
<?php endforeach?>
>

	<div class="gGrid">
		<?php if($confirmPassword): ?><div class="<?= $stackInputs ? 'span12' : 'span6 span12_600px' ?>"><?php endif ?>
			<label class="field-label" for="input_<?= $sanitizer->entities1($id) ?>">
				<i class="field-icon fa fa-fw fa-<?= $icon ?>"></i> <?= $sanitizer->entities1($label) ?>
				<?php if($required): ?><span class="field-required">*</span><?php endif ?>
			</label>

			<div class="passwordField-inputWrap">
				<input maxlength="<?= $sanitizer->entities1($maxLength) ?>" name="<?= $sanitizer->entities1($name) ?>[]" <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>"<?php endif ?> class="field-input txtBox <?= $sanitizer->entities1($error) ? 'txtBox_error' : '' ?>" type="password" placeholder="<?= $sanitizer->entities1($placeholder) ?>" value="<?= $showValue ? $sanitizer->entities($value[0]) : '' ?>" />

				<?php if($visibilityToggle): ?>
					<button type="button" class="passwordField-visibility btn btn_sm tooltip" tabindex="-1" title="Show/hide password"><i class="fa fa-eye"></i></button>
				<?php endif ?>
			</div>
		<?php if($confirmPassword): ?></div><?php endif ?>

		<?php if($confirmPassword): ?>
			<div class="<?= $stackInputs ? 'span12' : 'span6 span12_600px' ?>">
				<label class="field-label" for="input_<?= $sanitizer->entities1($id) ?>Confirm">
					<i class="field-icon fa fa-fw fa-<?= $sanitizer->entities1($icon) ?>"></i> <?= $sanitizer->entities1($labelConfirm) ?>
					<?php if($required): ?><span class="field-required">*</span><?php endif ?>
				</label>

				<input maxlength="<?= $sanitizer->entities1($maxLength) ?>" name="<?= $sanitizer->entities1($name) ?>[]" <?php if($id): ?>id="input_<?= $sanitizer->entities1($id) ?>Confirm"<?php endif ?> class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="password" placeholder="<?= $sanitizer->entities1($placeholderConfirm) ?>" value="<?= $showValue ? $sanitizer->entities($value[1]) : '' ?>" />
			</div>
		<?php endif ?>
	</div>

<?php include('../Field/footer.php') ?>