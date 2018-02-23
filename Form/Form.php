<?php if($markup): ?>
	<form id="<?= $id ?>" class="<?= $formClasses ?>" method="<?= $method ?>" <?= $action ? "action='$action'" : '' ?> target="<?= $target ?>" <?= $name ? "name='$name'" : '' ?> autocomplete="<?= $autocomplete ?>" enctype="multipart/form-data">
<?php endif ?>
	<?= $beforeForm ?>
	<div class="gGrid">
		<?= $fieldsOut ?>
		<?php if($method == 'post' && !$disableCSRF): ?>
			<?= $session->CSRF->renderInput() ?>
		<?php endif ?>
	</div>
	<?= $afterForm ?>
<?php if($markup): ?>
	</form>
<?php endif ?>