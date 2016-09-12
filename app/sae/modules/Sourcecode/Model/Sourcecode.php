<?php
class Sourcecode_Model_Sourcecode extends Core_Model_Default {

    public function __construct($params = array()) {
        parent::__construct($params);
        $this->_db_table = 'Sourcecode_Model_Db_Table_Sourcecode';
        return $this;
    }

    public function save() {
        parent::save();
        $this->cleanHtmlFile();
        return $this;
    }

    public function getHtmlFilePath() {

        if(!file_exists(Core_Model_Directory::getCacheDirectory(true).'/'.$this->_getFilename())) {
            $file = fopen(Core_Model_Directory::getCacheDirectory(true).'/'.$this->_getFilename(), 'w');

            $iframe_style_scroll = "";
            $html_code = $this->getHtmlCode();
            $html_a_target = "_top";
            if($this->getSession()->isOverview) {
                $html_a_target = "_blank";
            }

            $doc = new Dom_SmartDOMDocument();
            $doc->loadHTML($html_code);

            //detect if the code have an iframe
            if($doc->getElementsByTagName('iframe')->length != 0) {
                $iframe_style_scroll = "-webkit-overflow-scrolling:touch";
            }

            $links = $doc->getElementsByTagName('a');
            foreach ($links as $item) {
                $item->setAttribute('target', $html_a_target);
            }
            $html_code = html_entity_decode($doc->saveHTML(), ENT_QUOTES, "UTF-8");

            $html = '<html><head>
                <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
                <meta content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" name="viewport" />
                <meta content="black" name="apple-mobile-web-app-status-bar-style" />
                <meta content="IE=8" http-equiv="X-UA-Compatible" />
                <style type="text/css">
                html, body { margin:0; padding:0; border:none; }
                html { overflow: scroll; ' . $iframe_style_scroll . ' }
                body { font-size: 15px; width: 100%; height: 100%; overflow: auto; -webkit-user-select : none; -webkit-text-size-adjust : none; -webkit-touch-callout: none; line-height:1; background-color:white; }
                </style>
            </head><body>'.$html_code.'</body></html>';

            fputs($file, $html);
            fclose($file);
        }

        return Core_Model_Directory::getCacheDirectory().'/'.$this->_getFilename();

    }

    public function cleanHtmlFile() {
        if(file_exists(Core_Model_Directory::getCacheDirectory(true).'/'.$this->_getFilename())) {
            @unlink(Core_Model_Directory::getCacheDirectory(true).'/'.$this->_getFilename());
        }
        return $this;
    }

    protected function _getFilename() {
        $key = md5($this->getUpdatedAt()) . (int) $this->getSession()->isOverview;
        return 'html_content_'.$key.'_'.$this->getId().'.html';
    }

    public function getFeaturePaths($option_value) {

        if(!$this->isCachable()) return array();

        $paths = array();
        $paths[] = $option_value->getPath("find", array('value_id' => $option_value->getId()), false);

        $paths[] = $this->getBaseUrl().Core_Model_Directory::getCacheDirectory().'/'.$this->_getFilename();

        return $paths;

    }

    public function copyTo($option) {
        $this->setId(null)->setValueId($option->getId())->save();
        return $this;
    }
}
