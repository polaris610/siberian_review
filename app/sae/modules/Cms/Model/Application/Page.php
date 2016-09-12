<?php

class Cms_Model_Application_Page extends Core_Model_Default
{

    protected $_blocks;
    protected $_metadata;
    protected $_action_view = "findall";

    public function __construct($params = array())
    {
        parent::__construct($params);
        $this->_db_table = 'Cms_Model_Db_Table_Application_Page';
        return $this;
    }

    public function findByUrl($url)
    {
        $this->find($url, 'url');
        return $this;
    }

    public function getBlocks()
    {

        if (is_null($this->_blocks) AND $this->getId()) {
            $block = new Cms_Model_Application_Block();
            $this->_blocks = $block->findByPage($this->getId());
        } else {
            $this->_blocks = array();
        }

        return $this->_blocks;
    }

    public function getMetadatas()
    {
        $matadata = new Cms_Model_Application_Page_Metadata();
        $results = $matadata->findAll(array('page_id' => $this->getPageId()));
        $this->_metadata = array();
        foreach ($results as $result) {
            array_push($this->_metadata, $result);
        }
        return $this->_metadata;
    }

    public function getMetadata($code)
    {
        $metadata = new Cms_Model_Application_Page_Metadata();
        $metadata->find(array('page_id' => $this->getPageId(), 'code' => $code));
        return $metadata;
    }

    public function setMetadata($data)
    {
        foreach ($this->getMetadatas() as $metadatum) {
            // If there already are metadata with the given name update them
            if (array_key_exists($metadatum->getCode(), $data)) {
                $metadatum->setPayload($data[$metadatum->getCode()]);
                unset($data[$metadatum->getCode()]);
            } else {
                $metadatum->setPayload(null);
            }
        }
        // Otherwize create a new metadata and add it to the current metadata
        foreach ($data as $code => $payload) {
            array_push($this->_metadata, $this->_createMetadata($code, $payload));
        }
    }

    public function getMetadataValue($code)
    {
        $meta = $this->getMetadata($code);
        if ($meta) {
            return $meta->getPayload();
        } else {
            return null;
        }
    }

    protected function _createMetadata($code, $payload)
    {
        $meta = new Cms_Model_Application_Page_Metadata();
        $meta->setPageId($this->getPageId());
        $meta->setCode($code);
        $meta->setPayload($payload);
        return $meta;
    }

    public function getPictureUrl()
    {
        $path = Application_Model_Application::getImagePath() . $this->getPicture();
        $base_path = Application_Model_Application::getBaseImagePath() . $this->getPicture();
        return is_file($base_path) ? $path : null;
    }

    public function getFeaturePaths($option_value)
    {
        if (!$this->isCachable()) return array();

        if ($option_value->getCode() == "custom_page") return parent::getFeaturePaths($option_value);

        // Places paths
        $places = new Places_Model_Place();
        return $places->getFeaturePaths($option_value);
    }

    public function save()
    {
        parent::save();
        foreach ($this->_metadata as $metadatum) {
            $metadatum->save();
        }
        $blocks = $this->getData('block') ? $this->getData('block') : array();
        $this->getTable()->saveBlock($this->getId(), $blocks);
    }

    public function createDummyContents($option_value, $design, $category)
    {

        $option = new Application_Model_Option();
        $option->find($option_value->getOptionId());

        $dummy_content_xml = $this->_getDummyXml($design, $category);

        if ($option->getCode() == 'places' && $dummy_content_xml->places) {

            foreach ($dummy_content_xml->places->children() as $content) {

                $this->unsData();

                $blocks = array();
                $i = 1;
                foreach ($content->block as $block_content) {

                    $block = new Cms_Model_Application_Block();
                    $block->find((string)$block_content->type, "type");

                    $data = (array)$block_content;
                    if ($block_content->image_url) {
                        $data['image_url'] = (array)$block_content->image_url;
                        $data['image_fullsize_url'] = (array)$block_content->image_fullsize_url;
                    }
                    $data["block_id"] = $block->getId();

                    $blocks[$i++] = $data;
                }

                $this->addData((array)$content->content)
                    ->setBlock($blocks)
                    ->setValueId($option_value->getId())
                    ->save();
            }

        } else {

            $blocks = array();
            $i = 1;
            foreach ($dummy_content_xml->blocks->children() as $content) {

                $block = new Cms_Model_Application_Block();
                $block->find((string)$content->type, "type");

                $data = (array)$content;
                if ($content->image_url) {
                    $data['image_url'] = (array)$content->image_url;
                    $data['image_fullsize_url'] = (array)$content->image_fullsize_url;
                }
                $data["block_id"] = $block->getId();

                $blocks[$i++] = $data;
            }

            $this->setValueId($option_value->getId())
                ->setBlock($blocks)
                ->save();

        }
    }

    public function copyTo($option)
    {
        $blocks = array();

        foreach ($this->getBlocks() as $block) {
            switch ($block->getType()) {
                case 'image':
                    $library = new Cms_Model_Application_Page_Block_Image_Library();
                    $images = $library->findAll(array('library_id' => $block->getLibraryId()), 'image_id ASC', null);
                    $block->unsId(null)->unsLibraryId(null)->unsImageId();
                    $new_block = $block->getData();
                    $new_block['image_url'] = array();
                    $new_block['image_fullsize_url'] = array();
                    $new_block['library_id'] = null;
                    foreach ($images as $image) {
                        $new_block['image_url'][] = $image->getData('image_url');
                        $new_block['image_fullsize_url'][] = $image->getData('image_fullsize_url');
                    }
                    $blocks[] = $new_block;
                    break;
                case 'video':
                    $object = $block->getObject();
                    $object->setId(null);
                    $block->unsId(null)->unsVideoId();
                    $blocks[] = $block->getData() + $object->getData();
                    break;
                case 'address' :
                    $object = $block->getObject();
                    $object->setId(null);
                    $block->unsId(null)->unsAddressId();
                    $blocks[] = $block->getData() + $object->getData();
                    break;
                case 'button' :
                    $block->unsButtonId();
                case 'file' :
                    $block->unsFileId();
                case 'text' :
                default:
                    $block->unsId(null)->unsTextId();
                    $blocks[] = $block->getData();
                    break;
            }

        }

        $this->setData('block', $blocks);
        $this->setId(null)
            ->setValueId($option->getId())
            ->save();

    }

}
