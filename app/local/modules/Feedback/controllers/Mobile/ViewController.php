<?php

class Feedback_Mobile_ViewController extends Application_Controller_Mobile_Default
{
	public function findAction()
	{
		if ($value_id = $this->getRequest()->getParam('value_id')) {
			
			$data = array();
			$option = $this->getCurrentOptionValue();
			$data["page_title"] = $option->getTabbarName();
			$customer_id = $this->getSession()->getCustomerId();
			if (empty($customer_id)) {
				$customer_id = $this->getSession('front')->getAdminId();
				if (!$customer_id) {
					$customer1 = new Customer_Model_Db_Table_Customer();
					$row = $customer1->fetchRow();
					if ($row) {
						$rr = $row->toArray();
						$customer_id = $rr["customer_id"];
					}
				}
			}
			$data["customer_id"] = $customer_id;
			$feedback = $option->getObject();
			$rows = $feedback->findAll(array('value_id = ?' => $value_id), array('updated_at DESC'))->toArray();
			$data["overall"] = 0;
			$rateList = array();
			if (sizeof($rows) > 0) {
				$customer = new Customer_Model_Customer();
				$score_sum = 0;
				foreach ($rows as $key => $row) {
					$rateList[$key] = array();
					$rateList[$key]['feedback_content'] = $row['feedback_content'];
					$rateList[$key]['feedback_score'] = $row['feedback_score'] * 1;
					$customer->find($row['customer_id']);
					if (!$customer->getId()) {
						$rateList[$key]['user_name'] = '';
					} else {
						$rateList[$key]['user_name'] = ($customer->getData('firstname') . ' ' . $customer->getData('lastname'));
					}
					$score_sum += $row['feedback_score'] * 1;
				}
				$data["overall"] = round((($score_sum * 1) / sizeof($rows)), 1);
			}
			$data["rateList"] = $rateList;
			$this->_sendHtml($data);
		}
	}
	
	public function postAction()
	{
		if ($data = Zend_Json::decode($this->getRequest()->getRawBody())) {
			try {
				$data = $data["form"];
				$option = $this->getCurrentOptionValue();
				$feedback = $option->getObject();
				$errors = '';
				if ($data["customer_id"] == '') {
					$customer = new Customer_Model_Customer();
					$rEmail = $customer->findByEmail($data["user_email"]);
					if (!$rEmail->getId()) {
						$errors .= $this->_('Inputted user email is no exist!<br/>');
					} else {
						$data["customer_id"] = $rEmail->getId();
					}
				}
				$r = $feedback->findByCustomerId($data["value_id"], $data["customer_id"]);
				if (!$r) {
					$saveData = array(
						'value_id' => $data['value_id'],
						'customer_id' => $data['customer_id'],
						'feedback_content' => $data['feedback_content'],
						'feedback_score' => ($data['feedback_score'] * 1),
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					);
					$feedback->saveData($saveData, '');
				} else {
					$row = $r->toArray();
					$updateData = array(
						'feedback_content' => $data['feedback_content'],
						'feedback_score' => ($data['feedback_score'] * 1),
						'updated_at' => date('Y-m-d H:i:s')
					);
					$where = array('feedback_id = ?' => $row['feedback_id']);
					$feedback->saveData($updateData, $where);
				}
				if (empty($errors)) {
					$html = array(
						"success" => 1,
						"message" => $this->_("The feedback has been sent successfully")
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