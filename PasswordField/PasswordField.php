<div class="field passwordField" <?= $ajaxValidate ? 'data-ajax-validate="1"' : '' ?> <?= $ajaxSave ? 'data-ajax-save="1"' : '' ?>>

	<div class="gGrid">
		<?php if($confirmPassword): ?><div class="<?= $stackInputs ? 'span12' : 'span6 span12_600px' ?>"><?php endif ?>
			<label class="field-label" for="<?= $name ?>">
				<i class="field-icon fa fa-fw fa-<?= $icon ?>"></i> <?= $label ?>
				<?php if($required): ?><span class="field-required">*</span><?php endif ?>
			</label>

			<div class="passwordField-inputWrap">
				<input maxlength="<?= $maxLength ?>" name="<?= $name ?>[]" id="<?= $name ?>" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="password" placeholder="<?= $placeholder ?>" />

				<?php if($visibilityToggle): ?>
					<button type="button" class="passwordField-visibility btn btn_sm tooltip" tabindex="-1" title="Show/hide password"><i class="fa fa-eye"></i></button>
				<?php endif ?>
			</div>
		<?php if($confirmPassword): ?></div><?php endif ?>

		<?php if($confirmPassword): ?>
			<div class="<?= $stackInputs ? 'span12' : 'span6 span12_600px' ?>">
				<label class="field-label" for="<?= $name ?>Confirm">
					<i class="field-icon fa fa-fw fa-<?= $icon ?>"></i> <?= $labelConfirm ?>
					<?php if($required): ?><span class="field-required">*</span><?php endif ?>
				</label>

				<input maxlength="<?= $maxLength ?>" name="<?= $name ?>[]" id="<?= $name ?>Confirm" class="field-input txtBox <?= $error ? 'txtBox_error' : '' ?>" type="password" placeholder="<?= $placeholderConfirm ?>" />
			</div>
		<?php endif ?>
	</div>

<?php include('../Field/footer.php') ?>