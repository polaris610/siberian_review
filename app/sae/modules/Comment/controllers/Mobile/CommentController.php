<?php

class Comment_Mobile_CommentController extends Application_Controller_Mobile_Default {

    public function findallAction() {

        if($comment_id = $this->getRequest()->getParam('comment_id')) {

            $comment = new Comment_Model_Comment();
            $comment->find($comment_id);
            $customer = new Customer_Model_Customer();
            $noLogo = $customer->getImageLink();
            if($comment->getId()) {

                $answer = new Comment_Model_Answer();
                $answers = $answer->findByComment($comment->getId());
                $data = array();
                foreach($answers as $answer) {
                    $data[] = array(
                        "id" => $answer->getId(),
                        "name" => $answer->getCustomerName(),
                        "picture" => $this->getRequest()->getBaseUrl().$noLogo,
                        "message" => $answer->getText(),
                        "created_at" => $this->_durationSince($answer->getCreatedAt())
                    );

                }

                $this->_sendHtml($data);
            }

        }

    }

    public function addAction() {

        if($data = Zend_Json::decode($this->getRequest()->getRawBody())) {

            try {

                $customer_id = $this->getSession()->getCustomerId();

                if(empty($customer_id) OR empty($data['comment_id']) OR empty($data['text'])) {
                    throw new Exception($this->_("#105: An error occurred while saving"));
                }

                $comment_id = $data['comment_id'];
                $text = $data['text'];

                $answer = new Comment_Model_Answer();
                $answer->setCommentId($comment_id)
                    ->setCustomerId($customer_id)
                    ->setText($text)
                    ->save()
                ;

                $html = array('success' => 1);

                $message = $this->_('Your message has been successfully saved.');
                if(!$answer->isVisible()) $message .= ' ' . $this->_('It will be visible only after validation by our team.');
                else {

                    $customer = $this->getSession()->getCustomer();

                    $html["answer"] = array(
                        "author" => $customer->getFirstname() . ' ' . mb_substr($customer->getLastname(), 0, 1) . '.',
                        "picture" => $customer->getImageLink(),
                        "message" => $answer->getText(),
                        "created_at" => $answer->getFormattedCreatedAt()
                    );

                }

                $html["message"] = $message;

            }
            catch(Exception $e) {
                $html = array('error' => 1, 'message' => $e->getMessage());
            }

            $this->_sendHtml($html);
        }

    }

}