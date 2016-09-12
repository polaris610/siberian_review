<?php

class Sourcecode_ApplicationController extends Application_Controller_Default
{

    public function editpostAction() {

        if($datas = $this->getRequest()->getPost()) {

            try {

                $option_value = $this->getCurrentOptionValue();
                if(!$option_value) {
                    throw new Exception('An error occurred while saving. Please try again later.');
                }

                $html = '';
                $sourcecode = $option_value->getObject();
                if(!$sourcecode->getId()) {
                    $sourcecode->setValueId($option_value->getId());
                }
                $sourcecode->addData($datas)->save();

                $html = array(
                    'success' => '1',
                    'success_message' => $this->_('Info successfully saved'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                );

            }
            catch(Exception $e) {
                $html = array(
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                );
            }

            $this->getResponse()
                ->setBody(Zend_Json::encode($html))
                ->sendResponse()
            ;
            die;

        }

    }

}