<?php

class Form_Mobile_ViewController extends Application_Controller_Mobile_Default {

    public function findAction() {

        if($value_id = $this->getRequest()->getParam('value_id')) {

            $data = array("sections" => array());
            $option = $this->getCurrentOptionValue();
            $form = $option->getObject();
            $sections = $form->getSections();

            foreach($sections as $section) {

                $section_data = array(
                    "name" => $section->getName(),
                    "fields" => array()
                );

                $fields = $section->getFields();

                foreach($fields as $field) {

                    $field_data = array(
                        "id" => $field->getId(),
                        "type" => $field->getType(),
                        "name" => $field->getName(),
                        "options" => $field->hasOptions() ? $field->getOptions() : array()
                    );

                    if($field->isRequired()) {
                        $field_data["name"] .= " *";
                    }

                    $section_data["fields"][] = $field_data;
                }

                $data["sections"][] = $section_data;
            }

            $data["page_title"] = $option->getTabbarName();

            $this->_sendHtml($data);
        }

    }

    /**
     * Sauvegarde
     */
    public function postAction() {

        if ($data = Zend_Json::decode($this->getRequest()->getRawBody())) {
            try {

                $data = $data["form"];
                $data_image = array();
                $errors = '';
                // Recherche des sections
                $section = new Form_Model_Section();
                $sections = $section->findByValueId($this->getCurrentOptionValue()->getId());

                $field = new Form_Model_Field();

                // Date Validator
                $dataChanged = array();

                foreach ($sections as $k => $section) {
                    // Load the fields
                    $section->findFields($section->getId());
                    // Browse the fields
                    foreach($section->getFields() as $field) {

                        // If the field has options
                        if($field->hasOptions()) {

                            // If the data is not empty
                            if(isset($data[$field->getId()])) {
                                // if all checkbox = false
                                $empty_checkbox = false;
                                if(is_array($data[$field->getId()])){
                                    if(count($data[$field->getId()]) <= count(array_keys($data[$field->getId()], false))) {
                                        $empty_checkbox = true;
                                    }
                                }
                                if(!$empty_checkbox) {
                                    // Browse the field's options
                                    foreach ($field->getOptions() as $option) {

                                        // If it's a multiselect option and there's at least one selected option, store its value
                                        if (is_array($data[$field->getId()])) {
                                            // If the key exists,
                                            if (array_key_exists($option["id"], $data[$field->getId()])) {
//                                            $data[$field->getId()][$option["id"]] = $option["name"];
                                                if($data[$field->getId()][$option["id"]]) {
                                                    $dataChanged[$field->getName()][$option["id"]] = $option["name"];
                                                }
                                            }
                                            // If the current option has been posted, store its value
                                        } else if ($option["id"] == $data[$field->getId()]) {
//                                        $data[$field->getId()] = $option["name"];
                                            $dataChanged[$field->getName()] = $option["name"];
                                        }
                                    }
                                } else if($field->isRequired()) {
                                    $errors .= $this->_('<strong>%s</strong> must be filled<br />', $field->getName());
                                }

                                // If the field is empty and required, add an error
                            } else if($field->isRequired()) {
                                $errors .= $this->_('<strong>%s</strong> must be filled<br />', $field->getName());
                            }
                        } else {
                            // If the field is required
                            if($field->isRequired()) {
                                // Add an error based on its type (and if it's empty)
                                switch($field->getType()) {
                                    case "email":
                                        if(empty($data[$field->getId()]) OR !Zend_Validate::is($data[$field->getId()], 'EmailAddress')) {
                                            $errors .= $this->_('<strong>%s</strong> must be a valid email address<br />', $field->getName());
                                        }
                                        break;
                                    case "nombre":
                                        if(!isset($data[$field->getId()]) OR !Zend_Validate::is($data[$field->getId()], 'Digits')) {
                                            $errors .= $this->_('<strong>%s</strong> must be a numerical value<br />', $field->getName());
                                        }
                                        break;
                                    case "date":
                                        if(!isset($data[$field->getId()])/* OR !$validator->isValid($data[$field->getId()])*/) {
                                            $errors .= $this->_('<strong>%s</strong> must be a valid date (e.g. dd/mm/yyyy)<br />', $field->getName());
                                        }
                                        break;
                                    default:
                                        if(empty($data[$field->getId()])) {
                                            $errors .= $this->_('<strong>%s</strong> must be filled<br />', $field->getName());
                                        }
                                        break;
                                }
                            }

                            // If not empty, store its value
                            if(!empty($data[$field->getId()])) {
                                // If the field is an image
                                if($field->getType() == "image") {
                                    $image = $data[$field->getId()];

                                    if (!preg_match("@^data:image/([^;]+);@", $image, $matches)) {
                                        throw new Exception($this->_("Unrecognized image format"));
                                    }

                                    $extension = $matches[1];

                                    $fileName = uniqid() . '.' . $extension;
                                    $relativePath = $this->getCurrentOptionValue()->getImagePathTo();
                                    $fullPath = Application_Model_Application::getBaseImagePath() . $relativePath;
                                    if (!is_dir($fullPath)) mkdir($fullPath, 0777, true);
                                    $filePath = $fullPath . '/' . $fileName;

                                    $contents = file_get_contents($image);
                                    if ($contents === FALSE) {
                                        throw new Exception($this->_("No uploaded image"));
                                    }

                                    $res = file_put_contents($filePath, $contents);
                                    if ($res === FALSE) throw new Exception('Unable to save image');

                                    list($width, $height) = getimagesize($fullPath .DS. $fileName);

                                    $max_height = $max_width = 600;
                                    $image_name = uniqid($max_height);
                                    if($height > $width) {
                                        $image_width = $max_height * $width / $height;
                                        $image_height = $max_height;
                                    } else {
                                        $image_width = $max_width;
                                        $image_height = $max_width * $height / $width;
                                    }

                                    $newIcon = new Core_Model_Lib_Image();
                                    $newIcon->setId($image_name)
                                        ->setPath($fullPath .DS. $fileName)
                                        ->setWidth($image_width)
                                        ->setHeight($image_height)
                                        ->crop()
                                    ;
                                    $image_url = $this->getRequest()->getBaseUrl().$newIcon->getUrl();

                                    $dataChanged[$field->getName()] = '<br/><img width="'.$image_width.'" height="'.$image_height.'" src="'.$image_url.'" alt="'.$field->getName().'" />';
                                } else {
                                    $dataChanged[$field->getName()] = $data[$field->getId()];
                                }
                            }

                        }

                    }
                }

                if(empty($errors)) {

                    $form = $this->getCurrentOptionValue()->getObject();

                    $layout = $this->getLayout()->loadEmail('form', 'send_email');
                    $layout->getPartial('content_email')
                        ->setFields($dataChanged);
                    $content = $layout->render();

                    $mail = new Zend_Mail('UTF-8');
                    $mail->setBodyHtml($content);
                    $mail->setFrom($form->getEmail(), $this->getApplication()->getName());
                    $mail->addTo($form->getEmail(), $this->_('Your app\'s form'));
                    $mail->setSubject($this->_('Your app\'s form'));
                    $mail->send();

                    $html = array(
                        "success" => 1,
                        "message" => $this->_("The form has been sent successfully")
                    );
                } else {
                    $html = array('error' => 1, 'message' => $errors);
                }


            } catch (Exception $e) {
                $html = array('error' => 1, 'message' => $e->getMessage());
            }

            $this->_sendHtml($html);
        }
    }

}