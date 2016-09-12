<?php

class Form_Model_Form extends Core_Model_Default {

    protected $_sections;

    public function __construct($params = array()) {
        parent::__construct($params);
        $this->_db_table = 'Form_Model_Db_Table_Form';
        return $this;
    }

    public function getSections() {

        if(!$this->_sections) {
            $section = new Form_Model_Section();
            $this->_sections = $section->findAll(array('value_id' => $this->getValueId()));
        }

        return $this->_sections;

    }
    
    /**
     * Recherche par value_id
     * 
     * @param int $value_id
     * @return object
     */
    public function findByValueId($value_id) {
        return $this->getTable()->findByValueId($value_id);
    }

    public function createDummyContents($option_value, $design, $category) {

        $dummy_content_xml = $this->_getDummyXml($design, $category);

        foreach ($dummy_content_xml->children() as $content) {
            $this->unsData();
            $this->setEmail((string) $content->form->email)
                ->setValueId($option_value->getId())
                ->save()
            ;

            foreach ($content->form_sections->section as $section) {
                $section_obj = new Form_Model_Section();
                $section_obj->setName((string) $section->name)
                    ->setValueId($option_value->getId())
                    ->save()
                ;

                foreach ($section->fields->field as $field) {
                    $field_obj = new Form_Model_Field();
                    $field_obj->setSectionId($section_obj->getId())
                        ->addData((array) $field)
                        ->save()
                    ;
                }
            }
        }
    }

    public function copyTo($option) {

        $old_value_id = $this->getValueId();

        $this->setId(null)->setValueId($option->getId())->save();

        $section = new Form_Model_Section();
        $sections = $section->findAll(array('value_id' => $old_value_id));

        foreach($sections as $section) {

            $old_section_id = $section->getId();
            $section->setId(null)->setValueId($option->getId())->save();

            $field = new Form_Model_Field();
            $fields = $field->findAll(array('section_id' => $old_section_id));

            foreach($fields as $field) {
                $field->setId(null)
                    ->setSectionId($section->getId())
                    ->save()
                ;
            }

        }

        return $this;

    }

}
