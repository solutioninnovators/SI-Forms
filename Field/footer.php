	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<div class="field-error <?= !$error ? 'hide' : '' ?>"><i class="fa fa-caret-up"></i> <span class="field-errorTxt"><?= $sanitizer->entities1($error) ?></span></div>
    <div class="field-message <?= !$message ? 'hide' : '' ?>"><i class="fa fa-caret-up"></i> <span class="field-messageTxt"><?= $sanitizer->entities1($message) ?></span></div>

</div>