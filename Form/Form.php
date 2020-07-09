<?php if($markup): ?>
	<form id="<?= $id ?>" class="<?= $formClasses ?>" method="<?= $method ?>" <?= $action ? "action='$action'" : '' ?> target="<?= $target ?>" <?= $name ? "name='$name'" : '' ?> autocomplete="<?= $autocomplete ?>" <?= $ajaxSubmit ? 'data-ajax-submit=1' : '' ?> <?= $novalidate ? 'novalidate' : '' ?> enctype="multipart/form-data">
<?php endif ?>
	<?= $beforeForm ?>
	<div class="gGrid">
		<input type="hidden" name="form_<?= $id ?>" value="1" />
		<?= $fieldsOut ?>
		<?php if($method == 'post' && !$disableCSRF): ?>
			<?= $session->CSRF->renderInput() ?>
		<?php endif ?>
	</div>
	<?= $afterForm ?>
<?php if($markup): ?>
	</form>
<?php endif ?>