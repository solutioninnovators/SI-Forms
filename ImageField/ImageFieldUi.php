<?php namespace ProcessWire;
/**
 * Class ImageFieldUi
 * @package ProcessWire
 *
 * Designed to work together with a ProcessWire image field, so the savePage and saveField properties should be defined.
 *
 * To save the field after validation, call saveFileToPage()
 *
 * IMPORTANT: Note that unlike other html input types, html file inputs cannot be pre-populated with a current value. This means we cannot repopulate the field for the user if there is an error during the form save. Instead, this field uses $this->savePage and $this->saveField to sync its current state with the database value. Don't provide it with an initial value.
 *
 *  @todo: This field does not support ajax submit/save
 */
class ImageFieldUi extends FileFieldUi {
	public $maxSize = 20971520; // Size in Bytes
	public $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
	public $imgPreview = true;
	public $type = 'file'; // Tells the FormUI how to handle this field
	public $maxWidth = 800;
	public $maxHeight = 800;
    public $multiple = false;
	public $savePage = null;
	public $saveField = null;
	public $buttonClasses = '';
	public $cssClass = 'imageField';

	/**
	 * Call this after validating your form to save the validated image to the page specified by the $pageSave property.
	 * @return bool true on success, false on error
	 * @throws WireException
	 */
	public function saveFileToPage() {
		if(!$this->savePage || !$this->saveField) {
			throw new WireException('You must define the savePage and saveField options before trying to save an image field');
		}

		$value = $this->value;
		$error = '';

		if(!$value['tmp_name']) return; // Don't try to save if there's nothing to save

		$originalFileName = $value['name'];
		$newFileName = $this->wire('sanitizer')->fileName($originalFileName);

		// Move attachment into its own temp/quarantined folder so we can give it a nice file name
		$tempFile = $value['tmp_name'];
		$tmpDir = dirname($tempFile);
		$justTmpName = basename($tempFile);
		$newSubDir = "$tmpDir/folder_$justTmpName/";
		$quarantineFile = $newSubDir . $newFileName;

		if(mkdir($newSubDir)) {
			if(move_uploaded_file($tempFile, $quarantineFile)) { // Move the upload to a temporary "quarantine" area that is not web accessible

				// Resize the image proportionally if the width or height exceeds the bounding box
				$imageSizer = new ImageSizer($quarantineFile);

				$ratioX = $this->maxWidth / $imageSizer->getWidth();
				$ratioY = $this->maxHeight / $imageSizer->getHeight();
				$ratio = min($ratioX, $ratioY);

				$newWidth = (int)($imageSizer->getWidth() * $ratio);
				$newHeight = (int)($imageSizer->getHeight() * $ratio);

				$imageSizer->resize($newWidth, $newHeight);

				// Save the resized image to the page
				$of = $this->savePage->of();
				$this->savePage->of(false);
				$this->savePage->{$this->saveField}->removeAll();
				$this->savePage->{$this->saveField}->add($quarantineFile); // Add file to field on page
				if(!$this->savePage->save($this->saveField)) {
					$error = __("Could not save file to Page");
				}
				$this->savePage->of($of);

				unlink($quarantineFile); // Remove the file from the quarantine
			}
			else {
				$error = __("Could not move upload file to quarantine.");
			}

			rmdir($newSubDir); // Delete quarantine directory from the server
		}
		else {
			$error = __("Could not create temporary subdirectory.");
		}

		if($error) {
			$this->session->error = $error;
			$this->log->error($error);
			return false;
		}
		else return true;
	}
}