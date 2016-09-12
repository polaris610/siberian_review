<?php

class Cms_Application_PageController extends Application_Controller_Default {

    public function editpostAction() {

        if ($datas = $this->getRequest()->getPost()) {

            $html = '';

            try {
                // Test s'il y a un value_id
                if (empty($datas['value_id'])) {
                    throw new Exception($this->_('An error occurred while saving your page.'));
                }

                // Récupère l'option_value en cours
                $option_value = new Application_Model_Option_Value();
                $option_value->find($datas['value_id']);

                if(!$option_value->getId()) {
                    throw new Exception($this->_('An error occurred while saving your page.'));
                }

                $page = new Cms_Model_Application_Page();
                if(!empty($datas["page_id"])) {
                    if($datas["page_id"] == "new") unset($datas["page_id"]);
                    $page->find($datas["page_id"]);
                    if($page->getId() AND $page->getValueId() != $option_value->getId()) {
                        throw new Exception($this->_('An error occurred while saving your page.'));
                    }
                }

                if(empty($datas["picture"])) {

                    $datas["picture"] = null;

                } else if(file_exists(Core_Model_Directory::getTmpDirectory(true)."/".$datas["picture"])) {

                    $application = $this->getApplication();
                    $relative_path = $option_value->getImagePathTo();
                    $folder = Application_Model_Application::getBaseImagePath() . $relative_path;
                    $path = Application_Model_Application::getBaseImagePath() . $relative_path;
                    $file = Core_Model_Directory::getTmpDirectory(true).'/'.$datas['picture'];
                    if(!is_dir($path)) mkdir($path, 0777, true);
                    if(!copy($file, $folder.$datas['picture'])) {
                        throw new exception($this->_('An error occurred while saving. Please try again later.'));
                    } else {
                        $datas['picture'] = $relative_path.$datas['picture'];
                    }

                }

                // Traitement des images des blocks
                $blocks = !empty($datas['block']) && is_array($datas['block']) ? $datas['block'] : array();
                $image_path = $option_value->getImagePathTo().'/';
                $base_image_path = $this->getApplication()->getBaseImagePath() . $image_path;

                if (!is_dir($base_image_path)) {
                    mkdir($base_image_path, 0777, true);
                }

                foreach ($blocks as $k => $block) {
                    if (($block["type"] == "image" || $block["type"] == "slider") && !empty($block['image_url'])) {
                        foreach ($block['image_url'] as $index => $image_url) {
                            //déjà enregistrée
                            if (substr($image_url, 0, 1) != '/') {
                                if (!empty($image_url) AND file_exists(Core_Model_Directory::getTmpDirectory(true).'/'.$image_url)) {
                                    rename(Core_Model_Directory::getTmpDirectory(true).'/'.$image_url, $base_image_path . $image_url);
                                    $blocks[$k]['image_url'][$index] = $image_path . $image_url;
                                }
                            } else {
//                                $img = explode('/', $image_url);
//                                $img = $img[count($img) - 1];
//                                $blocks[$k]['image_url'][$index] = $image_path . $img;
                                $blocks[$k]['image_url'][$index] = $image_url;
                            }
                        }
                        foreach ($block['image_fullsize_url'] as $index => $image_url) {
                            //déjà enregistrée
                            if (substr($image_url, 0, 1) != '/') {
                                if (!empty($image_url) AND file_exists(Core_Model_Directory::getTmpDirectory(true).'/' . $image_url)) {
                                    rename(Core_Model_Directory::getTmpDirectory(true).'/'.$image_url, $base_image_path . $image_url);
                                    $blocks[$k]['image_fullsize_url'][$index] = $image_path . $image_url;
                                }
                            } else {
//                                $img = explode('/', $image_url);
//                                $img = $img[count($img) - 1];
//                                $blocks[$k]['image_fullsize_url'][$index] = $image_path . $img;
                                $blocks[$k]['image_fullsize_url'][$index] = $image_url;
                            }
                        }
                    }
                    if (($block["type"] == "text" || $block["type"] == "video") && !empty($block['image'])) {
                        //déjà enregistrée
                        if (substr($block['image'], 0, 1) != '/') {
                            if (!empty($block['image']) AND file_exists(Core_Model_Directory::getTmpDirectory(true).'/'.$block['image'])) {
                                rename(Core_Model_Directory::getTmpDirectory(true).'/'.$block['image'], $base_image_path . $block['image']);
                                $blocks[$k]['image'] = $image_path . $block['image'];
                            }
                        } else {
//                            $img = explode('/', $block['image']);
//                            $img = $img[count($img) - 1];
//                            $blocks[$k]['image'] = $image_path . $img;
                            $blocks[$k]['image'] = $block['image'];
                        }
                    }
                    if($block["type"] == "text") {
                        $blocks[$k]["content"] = stripslashes($block["content"]);
                    }
                    if($block["type"] == "button") {
                        if($block["type_id"] == "link") {
                            if(!empty($block["content"]) AND !(substr($block["content"], 0, strlen("http://")) === "http://") AND !(substr($block["content"], 0, strlen("https://")) === "https://")) {
                                $blocks[$k]["content"] = "http://".$block["content"];
                            }
                        }
                    }
                    if($block["type"] == "address" AND (empty($block["latitude"]) OR empty($block["longitude"]))) {
                        $latlon = Siberian_Google_Geocoding::getLatLng(array(
                            "address" => $block["address"]
                        ));

                        if(!empty($latlon[0]) AND !empty($latlon[1])) {
                            $blocks[$k]["latitude"] = $latlon[0];
                            $blocks[$k]["longitude"] = $latlon[1];
                        }
                    }
                    if($block["type"] == "file") {
                        if (!empty($block['name']) AND file_exists(Core_Model_Directory::getTmpDirectory(true).'/'.$block['name'])) {
                            rename(Core_Model_Directory::getTmpDirectory(true).'/'.$block['name'], $base_image_path . $block['name']);
                            $blocks[$k]['name'] = $image_path.$block['name'];
                        }
                    }
                }

                $datas['block'] = $blocks;

                $page->setMetadata($datas['metadata']);
                $page->setData($datas)->save();

                $html = array(
                    'success' => 1,
                    'success_message' => $this->_('Page successfully saved'),
                    'page_id' => $page->getId(),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                );
            } catch (Exception $e) {
                $html = array(
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                );
            }

            $this->getLayout()->setHtml(Zend_Json::encode($html));
        }
    }

    public function deleteAction() {

        if ($data = $this->getRequest()->getPost()) {

            $html = array();

            try {

                // Test s'il y a un value_id
                if (empty($data['option_value_id']) OR empty($data['id'])) {
                    throw new Exception($this->_('An error occurred while saving. Please try again later.'));
                }

                // Récupère l'option_value en cours
                $option_value = new Application_Model_Option_Value();
                $option_value->find($data['option_value_id']);

                if(!$option_value->getId()) {
                    throw new Exception($this->_('An error occurred while saving. Please try again later.'));
                }

                $page = new Cms_Model_Application_Page();
                $page->find($data["id"]);

                if(!$page->getId() OR $page->getValueId() != $option_value->getId() OR $option_value->getAppId() != $this->getApplication()->getId()) {
                    throw new Exception($this->_('An error occurred while saving your page.'));
                }

                $page->delete();

                $html = array(
                    'success' => 1,
                    'success_message' => $this->_('Page successfully deleted'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                );
            } catch (Exception $e) {
                $html = array(
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                );
            }

            $this->getLayout()->setHtml(Zend_Json::encode($html));
        }

    }

    public function addblockAction() {

        if ($datas = $this->getRequest()->getPost()) {

            try {

                $position = $this->getRequest()->getParam('position');

                if (empty($datas['block_id']))
                    throw new Exception($this->_('An error occurred during process. Please try again later.'));
                if (empty($position))
                    throw new Exception($this->_('An error occurred during process. Please try again later.'));

                $block = new Cms_Model_Application_Block();
                $block->find($datas['block_id']);

                if (!$block->getId())
                    throw new Exception($this->_('An error occurred during process. Please try again later.'));

                $html = array(
                    'success' => 1,
                );

                $html['layout'] = $this->getLayout()
                        ->addPartial('row', 'admin_view_default', $block->getTemplate())
                        ->setCurrentBlock($block)
                        ->setCurrentOptionValue($this->getCurrentOptionValue())
                        ->setPosition($position)
                        ->toHtml()
                ;
            } catch (Exception $e) {
                $html = array(
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                );
            }

            $this->getLayout()->setHtml(Zend_Json::encode($html));
        }
    }

    public function resizeAction() {
        try {

            $folder = Core_Model_Directory::getTmpDirectory(true).'/';

            $current_file = $this->getRequest()->getParam('file');

            $image_sizes = getimagesize($folder . $current_file);
            $src_width = $image_sizes[0];
            $src_height = $image_sizes[1];

            $params = array(
                'file' => $current_file,
                'source_width' => $src_width,
                'source_height' => $src_height,
                'crop_width' => $src_width,
                'crop_height' => $src_height,
                'output_width' => 400,
                'output_height' => 200,
                'w' => 400,
                'h' => 200
            );

            if ($src_width < $params['output_width'] || $src_height < $params['output_height']) {
                $source = imagecreatefromstring(file_get_contents($folder . $current_file));
                $dest_ratio = $params['output_width'] / $src_width;
                $dest_width = $params['output_width'];
                $dest_height = $src_height * $dest_ratio;

                $dest = ImageCreateTrueColor($dest_width, $dest_height);
                $trans_colour = imagecolorallocatealpha($dest, 0, 0, 0, 127);

                imagefill($dest, 0, 0, $trans_colour);
                imagecopyresized($dest, $source, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
                imagesavealpha($dest, true);
                imagepng($dest, $folder . $current_file, 0);
                $params["source_width"] = $dest_width;
                $params["source_height"] = $dest_height;
                $params["crop_width"] = $dest_width;
                $params["crop_height"] = $dest_height;
            }

            $x1 = ($params["source_width"] / 2) - ($params["output_width"] / 2);
            $y1 = ($params["source_height"] / 2) - ($params["output_height"] / 2);

            $params['x1'] = $x1;
            $params['y1'] = $y1;

            $uploader = new Core_Model_Lib_Uploader();
            $new_file = $uploader->savecrop($params);

            $datas = array(
                'success' => 1,
                'fullsize_file' => $current_file,
                'file' => $new_file,
                'message_success' => 'Enregistrement réussi',
                'message_button' => 0,
                'message_timeout' => 2,
            );
        } catch (Exception $e) {
            $datas = array(
                'error' => 1,
                'message' => $e->getMessage()
            );
        }
        $this->getLayout()->setHtml(Zend_Json::encode($datas));
    }

    /**
     * @todo have to move in a front controller
     */
    public function cropAction() {

        if($datas = $this->getRequest()->getPost()) {
            try {
                $html = array();
                $uploader = new Core_Model_Lib_Uploader();
                $file = $uploader->savecrop($datas);
                $html = array(
                    'success' => 1,
                    'file' => $file
                );
            } catch (Exception $e) {
                $html = array(
                    'error' => 1,
                    'message' => $e->getMessage()
                );
            }

            $this->_sendHtml($html);

        }

    }

    public function cropvideoAction() {
        if ($datas = $this->getRequest()->getPost()) {
            try {
                $uploader = new Core_Model_Lib_Uploader();
                $file = $uploader->savecrop($datas);
                $datas = array(
                    'success' => 1,
                    'file' => $file,
                    'message_success' => 'Enregistrement réussi',
                    'message_button' => 0,
                    'message_timeout' => 2,
                );
            } catch (Exception $e) {
                $datas = array(
                    'error' => 1,
                    'message' => $e->getMessage()
                );
            }
            $this->getLayout()->setHtml(Zend_Json::encode($datas));
        }
    }

}
