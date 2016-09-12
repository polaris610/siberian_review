<?php

class Comment_Mobile_ListController extends Application_Controller_Mobile_Default {

    private function _genericFindAction($value_id, $comments) {
        $application = $this->getApplication();
        $color = $application->getBlock('background')->getColor();

        $data = array(
            "collection" => array()
        );

        $icon_url = $application->getIcon(74);
        foreach($comments as $comment) {

            $customer = $comment->getCustomer();

            switch($this->getCurrentOptionValue()->getLayoutId()) {
                case 2:
                case 3:
                case 4:
                    $data['collection'][] = array(
                        "id" => $comment->getId(),
                        "title" => $comment->getTitle(),
                        "subtitle" => $comment->getSubtitle(),
                        "url" => $this->getPath("comment/mobile_view", array("value_id" => $value_id, "comment_id" => $comment->getId())),
                        "picture" => $comment->getImageUrl() ? $this->getRequest()->getBaseUrl().$comment->getImageUrl() : null,
                        "details" => array(
                            "date" => array(
                                "picto" => $this->_getColorizedImage($this->_getImage("pictos/pencil.png"), $color),
                                "text" => $this->_durationSince($comment->getCreatedAt())
                            ),
                            "comments" => array(
                                "picto" => $this->_getColorizedImage($this->_getImage("pictos/comment.png"), $color),
                                "text" => count($comment->getAnswers())
                            ),
                            "likes" => array(
                                "picto" => $this->_getColorizedImage($this->_getImage("pictos/heart.png"), $color),
                                "text" => count($comment->getLikes())
                            )
                        )
                    );
                break;
                case 1:
                default:
                    $data['collection'][] = array(
                        "id" => $comment->getId(),
                        "title" => $comment->getTitle(),
                        "subtitle" => $comment->getSubtitle(),
                        "message" => strip_tags(html_entity_decode(strip_tags($comment->getText()), ENT_NOQUOTES, "UTF-8")),
                        "url" => $this->getPath("comment/mobile_view", array("value_id" => $value_id, "comment_id" => $comment->getId())),
                        "author" => ($this->getCurrentOptionValue()->getCode() == "fanwall" && $customer->getFirstname()) ? $customer->getFirstname() : $application->getName(),
                        "icon" => $this->getRequest()->getBaseUrl().$icon_url,
                        "picture" => $comment->getImageUrl() ? $this->getRequest()->getBaseUrl().$comment->getImageUrl() : null,
                        "details" => array(
                            "date" => array(
                                "picto" => $this->_getColorizedImage($this->_getImage("pictos/pencil.png"), $color),
                                "text" => $this->_durationSince($comment->getCreatedAt())
                            ),
                            "comments" => array(
                                "picto" => $this->_getColorizedImage($this->_getImage("pictos/comment.png"), $color),
                                "text" => count($comment->getAnswers())
                            ),
                            "likes" => array(
                                "picto" => $this->_getColorizedImage($this->_getImage("pictos/heart.png"), $color),
                                "text" => count($comment->getLikes())
                            )
                        )
                    );
                break;
            }

        }

        $data['page_title'] = $this->getCurrentOptionValue()->getTabbarName();
        $data['code'] = $this->getCurrentOptionValue()->getCode();
        $data['displayed_per_page'] = Comment_Model_Comment::DISPLAYED_PER_PAGE;
        $data["header_right_button"]["picto_url"] = $this->_getColorizedImage($this->_getImage('pictos/comment_add.png', true), $this->getApplication()->getBlock('header')->getColor());

        $this->_sendHtml($data);
    }

    public function findallAction() {
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            $offset = $this->getRequest()->getParam("offset", 0);
            $comment = new Comment_Model_Comment();
            $comments = $comment->findAll(array("value_id" => $value_id, "is_visible = ?" => 1), "created_at DESC", array("offset" => $offset, "limit" => Comment_Model_Comment::DISPLAYED_PER_PAGE));
            $this->_genericFindAction($value_id, $comments);
        }
    }

    private function _getDistanceFromLatLonInKm($lat1,$lon1,$lat2,$lon2) {
        $lat_a = deg2rad($lat1);
        $lon_a = deg2rad($lon1);
        $lat_b = deg2rad($lat2);
        $lon_b = deg2rad($lon2);

        $distance = 2 * asin(sqrt(pow(sin(($lat_a-$lat_b)/2) , 2) + cos($lat_a)*cos($lat_b) * pow( sin(($lon_a-$lon_b)/2) , 2)));
        return $distance * 6371; // earth radius, in km
    }

    public function findnearAction() {
        if ($value_id = $this->getRequest()->getParam('value_id')) {

            $offset = $this->getRequest()->getParam('offset', 0);
            $latitude = $this->getRequest()->getParam('latitude');
            $longitude = $this->getRequest()->getParam('longitude');

            $comment = new Comment_Model_Comment();
            $comments = $comment->findAllWithLocation($value_id, $offset);

            $nearComments = array();

            foreach($comments as $comment) {
                /**
                 * @todo Set the proper value to $nearRadius
                 * @todo fix the loadMore function when _getDistanceFromLatLonInKm is used
                 */
//                $distance = $this->_getDistanceFromLatLonInKm($latitude, $longitude, $comment->getLatitude(), $comment->getLongitude());
//                $nearRadius = 100;
//                if ($distance < $nearRadius) {
                    $nearComments[] = $comment;
//                }
            }

            $this->_genericFindAction($value_id, $nearComments);
        }
    }

    public function detailsAction() {

        if($data = $this->getRequest()->getParams()) {

            try {
                if(empty($data['comment_id']) OR empty($data['option_value_id'])) {
                    throw new Exception($this->_('An error occurred during process. Please try again later.'));
                }

                $comment_id = $data['comment_id'];

                $comment = new Comment_Model_Comment();
                if($comment_id != 'new') {
                    $comment->find($comment_id);
                    if(!$comment->getId() OR $comment->getValueId() != $this->getCurrentOptionValue()->getId()) {
                        throw new Exception($this->_('An error occurred during process. Please try again later.'));
                    }
                }
                else {
                    $comment->setId($comment_id);
                }

                $html = $this->getLayout()->addPartial('view_details', 'core_view_mobile_default', "comment/l$this->_layout_id/view/details.phtml")
                    ->setCurrentComment($comment)
                    ->toHtml()
                ;

                $html = array('html' => $html, 'title' => $this->getApplication()->getName());

            }
            catch(Exception $e) {
                $html = array('error' => 1, 'message' => $e->getMessage());
            }

            $this->_sendHtml($html);
        }

    }

    public function addAction() {

        if($data = $this->getRequest()->getPost()) {

            try {

                $customer_id = $this->getSession()->getCustomerId();
                if(empty($customer_id) OR empty($data['status_id']) OR empty($data['text'])) {
                    throw new Exception('Erreur');
                }

                $comment_id = $data['status_id'];
                $text = $data['text'];

                $comment = new Comment_Model_Answer();
                $comment->setCommentId($comment_id)
                    ->setCustomerId($customer_id)
                    ->setText($text)
                    ->save()
                ;

                $message = $this->_('Your message has been successfully saved.');
                if(!$comment->isVisible()) $message .= ' ' . $this->_('It will be visible only after validation by our team.');

                $html = array('success' => 1, 'message' => $message);

            }
            catch(Exception $e) {
                $html = array('error' => 1, 'message' => $e->getMessage());
            }

            $this->_sendHtml($html);
        }

    }

    public function pullmoreAction() {
        if($data = $this->getRequest()->getParams()) {

            try {
                $comment = new Comment_Model_Comment();
                $comments = $comment->pullMore($data['option_value_id'], $data['pos_id'], $data['from'], Comment_Model_Comment::DISPLAYED_PER_PAGE);

                $partial_comment = '';
                $partial_details = '';
                foreach($comments as $comment) :
                    $partial_comment .= $this->getLayout()->addPartial('comment_'.$comment->getId(), 'core_view_mobile_default', 'comment/l1/view/item.phtml')
                        ->setCurrentComment($comment)
                        ->toHtml()
                    ;
                    $partial_details .= $this->getLayout()->addPartial('comment_details_'.$comment->getId(), 'core_view_mobile_default', 'comment/l1/view/details.phtml')
                        ->setCurrentComment($comment)
                        ->toHtml()
                    ;
                endforeach;

                $html = array(
                    'success' => 1,
                    'comments' => $partial_comment,
                    'details' => $partial_details
                );

            } catch(Exception $e) {
                $html = array('error' => 1, 'message' => $e->getMessage());
            }

            $this->_sendHtml($html);

        }

    }

}