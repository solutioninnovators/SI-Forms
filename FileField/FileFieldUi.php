<?php namespace ProcessWire;
/**
 * Class FileFieldUi
 * @package ProcessWire
 *
 * Designed to work together with a ProcessWire file field, so the savePage and saveField properties should be defined.
 *
 * To save the field after validation, call saveFileToPage()
 *
 * IMPORTANT: Note that unlike other html input types, html file inputs cannot be pre-populated with a current value. This means we cannot repopulate the field for the user if there is an error during the form save. Instead, this field uses $this->savePage and $this->saveField to sync its current state with the database value. Don't provide it with an initial value.
 *
 * @todo: This field does not support ajax submit/save
 *
 */
class FileFieldUi extends FieldUi {
    public $maxSize = 5242880; // Size in Bytes
    public $allowedExt = [];
    public $type = 'file'; // Tells the FormUI how to handle this field
    public $value = null;
    public $displayValue = null;
    public $multiple = false;
    public $savePage = null;
    public $saveField = null;
    public $buttonClasses = '';
    public $cssClass = 'fileField';

    public function run() {
        $this->displayValue = $this->getDisplayValue();
        parent::run();
    }

	public function isPopulated() {
        if($this->value['tmp_name']) {
            return true;
        }
        return false;
    }

    protected function getDisplayValue() {
        return ($this->savePage) ? $this->savePage->getUnformatted($this->saveField) : $this->value;
    }

    public function fieldValidate() {
        $value = $this->value;
        $abv = '';
        $fileSizes = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PT'];
        $x = 0;

        if (is_array($value['name'])) {
            if ($value['name'][0] !== "") {
                for ($x = 0; $x < count($value['name']); $x++) {
                    $ext = strtolower(pathinfo($value['name'][$x], PATHINFO_EXTENSION)); // Get file extension
                    if ($value['size'][$x] > $this->maxSize) {
                        while (($this->maxSize / pow(1024, $x)) >= 1) {
                            $fraction = round($this->maxSize / pow(1024, $x), 1);
                            $abv = $fileSizes[$x];
                            $x++;
                        }
                        $this->error = __('File size must be less than ') . $fraction . __(" $abv.");
                    } elseif (!count($this->allowedExt)) {
                        if (!in_array($ext, $this->allowedExt)) {
                            $allowedExtString = implode(', ', $this->allowedExt);
                            $this->error = __("File must be one of the following: ") . $allowedExtString;
                        }
                    } elseif ($value['error'][$x]) {
                        $this->error = __("Upload error");
                    }
                }
            } else {
                $this->value = $this->getDisplayValue();
            }
        } else {
            $ext = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION)); // Get file extension

            if ($value['size'] > $this->maxSize) {
                while (($this->maxSize / pow(1024, $x)) >= 1) {
                    $fraction = round($this->maxSize / pow(1024, $x), 1);
                    $abv = $fileSizes[$x];
                    $x++;
                }
                $this->error = __('File size must be less than ') . $fraction . __(" $abv.");
            } elseif (!count($this->allowedExt)) {
                if (!in_array($ext, $this->allowedExt)) {
                    $allowedExtString = implode(', ', $this->allowedExt);
                    $this->error = __("File must be one of the following: ") . $allowedExtString;
                }
            } elseif ($value['error']) {
                $this->error = __("Upload error");
            }
        }
        if ($this->error) return false;
        else return true;
    }



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
        // Move attachment into its own temp/quarantined folder so we can give it a nice file name
        if (is_array($value['tmp_name'])) {
            foreach($value['tmp_name'] as $key => $tempName) {
                $this->addFileToField($value['name'][$key], $tempName);
            }
        } else {
            $this->addFileToField($value['name'], $value['tmp_name']);
        }
        $this->value = $this->getDisplayValue();
    }

    public function addFileToField($value, $tempName) {
        $error = '';
        if(!$tempName) return; // Don't try to save if there's nothing to save
        $originalFileName = $value;
        $newFileName = $this->wire('sanitizer')->fileName($originalFileName);

        $tempFile = $tempName;
        $tmpDir = dirname($tempFile);
        $justTmpName = basename($tempFile);
        $newSubDir = "$tmpDir/folder_$justTmpName/";
        $quarantineFile = $newSubDir . $newFileName;

        if(mkdir($newSubDir)) {
            if(move_uploaded_file($tempFile, $quarantineFile)) { // Move the upload to a temporary "quarantine" area that is not web accessible
                // Save the resized image to the page
                $of = $this->savePage->of();
                $this->savePage->of(false);
                if (!$this->multiple) {
                    $this->savePage->{$this->saveField}->removeAll();
                }
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

    public function ajax_removeFileFromPage($data) {
        if(FormUi::isCallback($this->saveCallback)) {
            $this->value = '';
            call_user_func_array($this->saveCallback, [$this, $this->form]);
        } else {
        $savePage = $this->savePage;
        $savePage->of(false);
        $pageFiles = $savePage->{$this->name};
        foreach ($pageFiles as $pageFile) {
            if ($pageFile->name == $data['fileName']) {
                $savePage->{$this->name}->delete($pageFile);
                $savePage->save();
                $savePage->of(true);
            }
        }
    }
    }

    /**
     * @throws WireException
     */
    public function downloadFile($data) {
        $savePage = $this->savePage;
        $savePage->of(false);
        $pageFiles = $savePage->{$this->name};
        foreach ($pageFiles as $pageFile) {
            if ($pageFile->name == $data['fileName']) {
                wireSendFile($pageFile->filename(), array('forceDownload' => true));
            }
        }
    }

    /**
     * General purpose function to move the currently uploaded file to a new location and update the current value
     * @param $toPath
     * @param bool $is_upload determines whether to use $_FILES for source OR file system for source
     * @return bool whether the move succeeded or not
     */
    public function moveTo($toPath, $is_upload = FALSE) {
        $fromPath = $this->value;
        if(empty($fromPath) OR !is_string($fromPath)) return FALSE;

        // make sure all folder paths exists
        $toDir = dirname($toPath);
        if(!is_dir($toDir)) {
            // IMPORTANT: Setting the recursive flag so if folders along the tree doesn't exist, it will be auto created
            if(!mkdir($toDir, 0777, TRUE)) {
                $error = __("Could not create the upload directory: " . $toDir);
            }
        }

        if(is_dir($toDir)) {
            // Remember, we can only use move_uploaded_file for uploads in the $_FILES array, for others, we have to rely on files
            $result = $is_upload ? move_uploaded_file($fromPath, $toPath) : rename($fromPath, $toPath);
            if ($result) {
                // NOTE: We are updating the file, because after move, the current value won't exist
                $this->value = $toPath;
                return TRUE;
            } else {
                $error = __("Could not move uploaded file to target location: " . $toPath);
            }
        }

        // pass errors to both logger and display to user
        if(!empty($error)) {
            $this->session->error = $error;
            $this->log->error($error);
        }
        return FALSE;
    }
}