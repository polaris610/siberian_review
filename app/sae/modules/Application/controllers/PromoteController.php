<?php

class Application_PromoteController extends Application_Controller_Default {

    public function indexAction() {
        $this->loadPartials();
    }

    public function qrcodeAction() {

//        if($this->getApplication()->getDomain()) {
            $qrcode = file_get_contents($this->getApplication()->getQrcode(null, array('size' => '512x512', 'without_template' => 1)));
            if($qrcode) {
                $this->_download($qrcode, 'qrcode.png', 'image/png');
            }
            else {
                $this->getSession()->addError($this->_('An error occurred during the generation of your QRCode. Please try again later.'));
                $this->_redirect('application/promote');
            }
//        }

    }

    public function savestoresAction() {
        if($data = $this->getRequest()->getPost()) {

            try {

                /**
                 * Save iOS store URL
                 **/
                if(!empty($data['ios_store_url'])) {
                    $ios_store = $data['ios_store_url'];

                    if(stripos($ios_store, "http") === false) {
                        $ios_store = "http://".$ios_store;
                    }
                    if(!Zend_Uri::check($ios_store)) {
                        throw new Exception($this->_("Please enter a correct URL for the %s store", "iOS"));
                    }
                } else {
                    $ios_store = null;
                }

                $device = $this->getApplication()->getDevice(1);
                $device->addData(array("store_url" => $ios_store))->save();

                /**
                 * Save Android store URL
                 **/
                if(!empty($data['android_store_url'])) {
                    $android_store = $data['android_store_url'];

                    if(stripos($android_store, "http") === false) {
                        $android_store = "http://".$android_store;
                    }
                    if(!Zend_Uri::check($android_store)) {
                        throw new Exception($this->_("Please enter a correct URL for the %s store", "Android"));
                    }
                } else {
                    $android_store = null;
                }

                $device = $this->getApplication()->getDevice(2);
                $device->addData(array("store_url" => $android_store))->save();

                $html = array(
                    'success' => '1',
                    'success_message' => $this->_('Info successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                );

            }
            catch(Exception $e) {
                $html = array('message' => $e->getMessage());
            }

            $this->_sendHtml($html);
        }
    }

}
