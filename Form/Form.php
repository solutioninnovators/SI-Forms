<?php if($markup): ?>
	<form id="<?= $sanitizer->entities1($id) ?>" class="<?= $sanitizer->entities1($formClasses) ?>" method="<?= $sanitizer->entities1($method) ?>" <?= $action ? "action='$action'" : '' ?> target="<?= $sanitizer->entities1($target) ?>" <?php if($name): ?>name=<?= $sanitizer->entities1($name) ?><?php endif ?> autocomplete="<?= $sanitizer->entities1($autocomplete) ?>" <?= $ajaxSubmit ? 'data-ajax-submit=1' : '' ?> <?= $novalidate ? 'novalidate' : '' ?> enctype="multipart/form-data">
<?php endif ?>
	<?= $beforeForm ?>
	<div class="gGrid">
		<input type="hidden" name="form_<?= $sanitizer->entities1($id) ?>" value="1" />
		<?= $fieldsOut ?>
		<?php if($method == 'post' && !$disableCSRF): ?>
			<?= $session->CSRF->renderInput() ?>
		<?php endif ?>
	</div>
	<?= $afterForm ?>
<?php if($markup): ?>
	</form>
<?php endif ?>