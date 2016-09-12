<?php

class Application_Model_SourceQueue extends Core_Model_Default {

    const ARCHIVE_FOLDER = "/var/tmp/jobs/";

    public $_devices = array(
        "ios" => 1,
        "iosnoads" => 1,
        "android" => 2,
    );

    public function __construct($data = array()) {
        parent::__construct($data);
        $this->_db_table = "Application_Model_Db_Table_SourceQueue";
    }

    /**
     * @param $status
     * @return mixed
     */
    public function changeStatus($status) {
        switch($status) {
            case "building":
                $this->setBuildTime(time());
                break;
            case "success":
                $this->setBuildTime(time() - $this->getBuildTime());
                break;
            default:
                $this->setBuildTime(0);
        }

        return $this->setStatus($status)->save();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function generate() {
        $application = new Application_Model_Application();
        $application = $application->find($this->getAppId());

        if(!$application->getId()) {
            throw new Exception(__("#500-02: This application does not exist"));
        }

        $design_code = (in_array($this->getDesignCode(), array("angular", "ionic"))) ? $this->getDesignCode() : "ionic";

        $application->setDesignCode($design_code);
        $device = $application->getDevice($this->_devices[$this->getType()]);
        $device->setApplication($application);
        $device->setExcludeAds(($this->getType()=="iosnoads"));
        $device->setDownloadType("zip");
        $device->setHost($this->getHost());

        $result = $device->getResources();

        if(file_exists($result)) {
            $this->changeStatus("success");
            $this->setPath($result);

        } else {
            $this->changeStatus("failed");
        }

        $this->save();

        if($this->getIsAutopublish()) {
            $this->sendJobToAutoPublishServer($application, $result);
        }

        return $result;
    }

    protected function sendJobToAutoPublishServer($application, $sourcePath) {
        $app_id = $application->getId();

        //ios license key
        $config = new System_Model_Config();
        $config->find("ios_autobuild_key","code");
        $license_key = $config->getValue();

        //application infos
        $app = new Application_Model_Application();
        $app->find($app_id);

        //backoffice user
        $user = new Backoffice_Model_User();
        $user->find($this->getUserId());
        $usermail = $user->getEmail();

        //ios setting info
        $appIosAutopublish = new Application_Model_IosAutopublish();
        $appIosAutopublish->find($app_id, "app_id");

        if($languages = Zend_Json::decode($appIosAutopublish->getLanguages())) {
            if(count($languages) === 0) {
                throw new Exception("There is no language selected");
            }
        } else {
            throw new Exception("Cannot unserialize language data");
        }

        $data = array(
            "name" => $app->getName(),
            "bundle_id" => $app->getBundleId(),
            "want_to_autopublish" => $appIosAutopublish->getWantToAutopublish(),
            "itunes_login" => $appIosAutopublish->getItunesLogin(),
            "itunes_password" => $appIosAutopublish->getItunesPassword(),
            "has_bg_locate" => $appIosAutopublish->getHasBgLocate(),
            "has_audio" => $appIosAutopublish->getHasAudio(),
            "languages" => $languages,
            "host" => $this->getHost(),
            "license_key" => $license_key,
            "token" => $appIosAutopublish->getToken(),
            "email" => $usermail,
        );

        $jobCode = time().'-'.$appIosAutopublish->getToken();
        $jobFolder = Core_Model_Directory::getBasePathTo(self::ARCHIVE_FOLDER.$jobCode);

        if(!mkdir($jobFolder,0777,true)) {
            throw new Exception("Cannot create folder $jobFolder");
        }

        if(!copy($sourcePath, $jobFolder."/sources.zip")) {
            throw new Exception("Cannot copy sources to job folder");
        }

        $configJobFilePath = $jobFolder."/config.json";

        if($json = Zend_Json::encode($data)) {
            file_put_contents($configJobFilePath, $json);
        } else {
            throw new Exception("Cannot create json config job file");
        }

        $tgzJobFilePath = $jobFolder . '.tgz';

        exec("tar zcf $tgzJobFilePath -C $jobFolder sources.zip config.json", $output, $return_val);

        if($return_val !== 0) {
            throw new Exception("Cannot create zip job file");
        }

        $jobUrlEncoded = base64_encode('http://'.$this->getHost().'/var/tmp/jobs/'.$jobCode.'.tgz');

        exec("curl -u ios-builder:ced2eb561db43afb09c633b8f68c1f17 http://jenkins.xtraball.com/job/ios-autopublish/buildWithParameters?token=2a66b48d4a926a23ee92195d73251c22\&SIBERIAN_JOB_URL=$jobUrlEncoded 2>&1",$output,$return_val);

        if($return_val !== 0) {
            throw new Exception("Cannot start job process");
        }
    }

    /**
     * Fetch if some apps are building.
     *
     * @param $application_id
     * @return array
     */
    public static function getStatus($application_id) {
        $table = new self();
        $results = $table->findAll(array(
            "app_id" => $application_id,
            "status IN (?)" => array("queued", "building"),
        ));

        $data = array(
            "ios" => false,
            "iosnoads" => false,
            "android" => false,
        );
        
        foreach($results as $result) {
            $type = $result->getType();
            if(array_key_exists($type, $data)) {
                # Set is building
                $data[$type] = true;
            }
        }

        return $data;
    }

    /**
     * Fetch if some apps are done.
     *
     * @param $application_id
     * @return array
     */
    public static function getPackages($application_id) {
        $table = new self();
        $results = $table->findAll(array(
            "app_id" => $application_id,
            "status IN (?)" => array("success"),
        ), array("updated_at DESC"));

        $base_path = Core_Model_Directory::getBasePathTo("");
        $data = array();

        foreach($results as $result) {
            $type = $result->getType();
            if(!array_key_exists($type, $data)) {
                # Set is building
                $data[$type] = array(
                    "path" => str_replace($base_path, "", $result->getData("path")), /** Frakking conflict */
                    "date" => $result->getFormattedUpdatedAt()
                );
            }
        }

        return $data;
    }

    /**
     * @return Application_Model_SourceQueue[]
     */
    public static function getQueue() {
        $table = new self();
        $results = $table->findAll(
            array("status IN (?)" => array("queued")),
            array("created_at ASC")
        );

        return $results;
    }
    
}
