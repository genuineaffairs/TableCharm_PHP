<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Edit
 *
 * @author abakivn
 */
class Zulu_Controller_Action_Helper_EditUser extends Zend_Controller_Action_Helper_Abstract {

    protected $_form;
    protected $_formClass = 'Zulu_Form_Edit_Photo';

    public function uploadPhoto() {
        $user = $this->getActionController()->view->user;
        $viewer = $this->getActionController()->view->viewer;

        // Get form
        $this->getForm();

//        if (empty($user->photo_id)) {
//            $this->_form->removeElement('remove');
//        }

        if (!$this->getActionController()->getRequest()->isPost()) {
            return;
        }

        if (!$this->_form->isValid($this->getActionController()->getRequest()->getPost())) {
            return;
        }

        // Uploading a new photo
        if ($this->_form->Filedata->getValue() !== null) {
            $db = $user->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $fileElement = $this->_form->Filedata;

                $user->setPhoto($fileElement);

                $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

                // Insert activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

                // Hooks to enable albums to work
                if ($action) {
                    $event = Engine_Hooks_Dispatcher::_()
                            ->callEvent('onUserProfilePhotoUpload', array(
                        'user' => $user,
                        'file' => $iMain,
                    ));

                    $attachment = $event->getResponse();
                    if (!$attachment)
                        $attachment = $iMain;

                    // We have to attach the user himself w/o album plugin
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                }

                $db->commit();
            }

            // If an exception occurred within the image adapter, it's probably an invalid image
            catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $this->_form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            }

            // Otherwise it's probably a problem with the database or the storage system (just throw it)
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        // Resizing a photo
        else if ($this->_form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();

            $iProfile = $storage->get($user->photo_id, 'thumb.profile');
            $iSquare = $storage->get($user->photo_id, 'thumb.icon');

            // Read into tmp file
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);

            list($x, $y, $w, $h) = explode(':', $this->_form->getValue('coordinates'));

            $image = Engine_Image::factory();
            $image->open($pName)
                    ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                    ->write($iName)
                    ->destroy();

            $iSquare->store($iName);

            // Remove temp files
            @unlink($iName);
        }

        return $this;
    }

    public function getForm() {
        if (is_null($this->_form)) {
            $this->_form = new $this->_formClass();
        }

        return $this->_form;
    }

    public function setFormClass($name) {
        if (is_string($name)) {
            $this->_formClass = $name;
        }
    }

}
