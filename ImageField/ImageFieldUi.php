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
 * NOTE: This field does not support ajax submit/save by default, unless using the new FormData submission in the FormUi
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

    // todo: add ability to generate unique file name automatically to avoid name collisions
    // determines whether to automatically move the file to quarantine area, and resize the image
    public $autoProcessFile = FALSE;
    // this flag determines whether a file was uploaded recently to this field, useful when determining whether
    // a file was processed during form saves
    public $fileUploaded = FALSE;
    // used to add preview to the value, when not using savePage logic
    public $imgPreviewPath = '';

    public function save($forceSaveNow = false)
    {
        // only process it once per form submit
        if($this->autoProcessFile && !$this->fileUploaded) {
            $this->moveAndResize();
        }

        return parent::save($forceSaveNow);
    }

    private function moveAndResize() {
        $value = $this->value;
        if(!is_array($value) OR empty($value['tmp_name'])) return false; // Don't try to save if there's nothing to save

        $originalFileName = $value['name'];
        $newFileName = $this->wire('sanitizer')->fileName($originalFileName);

        // Move attachment into its own temp/quarantined folder so we can give it a nice file name
        $tempFile = $value['tmp_name'];
        $tmpDir = dirname($tempFile);
        $justTmpName = basename($tempFile);
        $newSubDir = "$tmpDir/folder_$justTmpName/";
        $quarantineFile = $newSubDir . $newFileName;

        // todo: consider using WireUpload? (looks like it already has this functionality)
        // ex: $ul = $this->wire(new WireUpload($imageField->value));
        //                    //$ul->setValidExtensions(array('zip'));
        //                    $ul->setMaxFiles(1);
        //                    $ul->setOverwrite(true);
        //                    $ul->setDestinationPath($this->wire->config->paths->assets . 'products/' . $id . '/');
        //                    $ul->setExtractArchives(false);
        //                    $ul->setLowercase(false);

        // this is needed so moveTo can reference the correct original file path
        $this->value = $tempFile;
        if($this->moveTo($quarantineFile, TRUE)) {
            // Resize the image proportionally if the width or height exceeds the bounding box
            $imageSizer = new ImageSizer($quarantineFile);

            $ratioX = $this->maxWidth / $imageSizer->getWidth();
            $ratioY = $this->maxHeight / $imageSizer->getHeight();
            $ratio = min($ratioX, $ratioY);

            $newWidth = (int)($imageSizer->getWidth() * $ratio);
            $newHeight = (int)($imageSizer->getHeight() * $ratio);

            $imageSizer->resize($newWidth, $newHeight);

            // This is where we update the value to the newly moved file
            $this->fileUploaded = TRUE;
            $this->value = $quarantineFile;
            return TRUE;
        }
        return FALSE;
    }

	/**
     * todo: utilize the above (only difference is setting $this->value and unlinks)
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