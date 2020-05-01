	<?php if($notes): ?>
		<div class="field-notes"><?= $notes ?></div>
	<?php endif ?>

	<div class="field-error <?= !$error ? 'hide' : '' ?>"><i class="fas fa-exclamation-circle"></i> <span class="field-errorTxt"><?= $error ?></span></div>
    <div class="field-message <?= !$message ? 'hide' : '' ?>"><i class="fas fa-info-circle"></i> <span class="field-messageTxt"><?= $message ?></span></div>

</div>