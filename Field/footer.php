	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<div class="field-error" <?= !$error ? 'style="display:none"' : '' ?>><i class="fa fa-caret-up"></i> <span class="field-errorTxt"><?= $sanitizer->entities1($error) ?></span></div>
    <div class="field-message" <?= !$error ? 'style="display:none"' : '' ?>><i class="fa fa-caret-up"></i> <span class="field-messageTxt"><?= $sanitizer->entities1($message) ?></span></div>

</div>