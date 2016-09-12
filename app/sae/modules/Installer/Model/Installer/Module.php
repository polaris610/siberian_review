<?php

class Installer_Model_Installer_Module extends Core_Model_Default
{

    const DEFAULT_VERSION = '0.0.1';

    public $_lastVersion;

    protected $_name;
    protected $_dbFiles = array();
    protected $_schemaFiles = array();
    protected $_isInstalled = false;
    protected $_packageInfo = null;
    protected $_basePath;

    public function __construct($config = array()) {
        $this->_db_table = 'Installer_Model_Db_Table_Installer_Module';
        parent::__construct($config);
    }

    public function prepare($name, $fetch = true) {

        $this->_name = $name;
        $this->findByName($name);

        if(!$this->getId()) {
            $this->setName($name)
                ->setVersion(self::DEFAULT_VERSION)
            ;
            $this->_isInstalled = false;
        }
        else {
            $this->_isInstalled = true;
        }

        if($fetch) {
            $this->fetchModule($name);
        }

        return $this;
    }

    public function reset() {
        $this->_lastVersion = null;
        $this->_dbFiles = array();
        $this->_isInstalled = false;
        $this->_basePath = null;
        return $this;
    }

    public function findByName($name) {

        if($this->getTable()->isInstalled()) {
            $this->find($name, 'Name');
        }

        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    /**
     * Whether the module is installed or not
     *
     * @return bool
     */
    public function isInstalled() {
        return $this->_isInstalled;
    }

    public function canUpdate() {
        return version_compare($this->_lastVersion, $this->getVersion(), '>');
    }

    public function install() {

        if($this->canUpdate()) {

            # Syncing DB only if needed
            $migration_tables = array();
            foreach ($this->_schemaFiles as $table_name => $filename) {
                $migration_table = new Siberian_Migration_Db_Table($table_name);
                $migration_table->setSchemaPath($filename);

                # Test if table exist, if yes try to update, otherwise, try to create.
                $migration_table->tableExists();

                $migration_tables[] = $migration_table;
            }

            # Dependencies injector (mainly for installation purpose)
            if(isset($this->_packageInfo["dependencies"]["modules"])) {
                foreach($this->_packageInfo["dependencies"]["modules"] as $module => $version) {
                    $depModule = new Installer_Model_Installer_Module();
                    $depModule->prepare($module);

                    if(!$depModule->isInstalled()) {
                        $depModule->install();
                    }

                    if(!version_compare($depModule->_lastVersion, $version, ">=")) {
                        throw new Exception("The installed module {$module}@{$depModule->getVersion()} does not satisfy required {$module}@{$version}, aborting.");
                    }
                }
            }

            # Now update the foreign keys.
            foreach ($migration_tables as $table) {
                $table->updateForeignKeys();
            }

        }

        # Testing if it's a Template installer
        $template_install_path = Core_Model_Directory::getBasePathTo('var/tmp/template.install.php');
        if(is_readable($template_install_path)) {
            $this->_installTemplate($template_install_path);
            unlink($template_install_path);
        }
    }

    public function insertData() {

        # Processing data files
        foreach($this->_dbFiles as $index => $file) {

            if(preg_match("/.*install\.php$/", $file)) {
                /** Backward compatibiliy (mainly for our modules) */
                if(!$this->isInstalled()) {
                    $this->_run($file);
                }

            } else if(preg_match("/.*([0-9\.]+)\.php$/", $file)) {
                # Never call again old format files (thus they must never pop as the path changed)
            } else {
                $this->_run($file);
            }

            $this->save();
        }

        # Set the version to the last in package.json
        if(version_compare($this->_lastVersion, $this->getVersion(), '>')) {
            $this->setVersion($this->_lastVersion)->save();
        }
    }

    protected function readPackage($path) {
        $content = file_get_contents($path);

        if(!empty($content)) {
            return json_decode($content, true);
        }

        return false;
    }

    /** Fetching from sae to local */
    protected function fetchModule($module_name) {
        $basePath = Core_Model_Directory::getBasePathTo("app/sae/modules/{$module_name}");
        $editions = Siberian_Cache_Design::$editions[strtolower(Siberian_Version::TYPE)];

        /** fetching package.json */
        $package_info = false;
        $package_files = array();
        foreach($editions as $edition) {
            $folder = str_replace("/sae/", "/$edition/", $basePath);
            if(is_readable($folder."/package.json")) {
                $package_files[] = $folder."/package.json";
                # Don't break in case another package.json exists
            }
        }

        //we get higher package version
        $highest_package_version = "0.0.0";
        foreach ($package_files as $package_file) {
            $current_package_info = $this->readPackage($package_file);
            if(version_compare($current_package_info["version"], $highest_package_version, '>')) {
                $package_info = $current_package_info;
                $highest_package_version = $current_package_info["version"];
            }
        }

        if(!$package_info) {
            trigger_error("Error: 'package.json' is missing from {$module_name}, unable to install/update the Module.", E_USER_WARNING);
            return;
        }

        $this->_packageInfo = $package_info;

        /** Get the schema for installation/sync */
        foreach($editions as $edition) {
            $folder = str_replace("/sae/", "/$edition/", $basePath);
            if(is_readable($folder."/resources/db/schema")) {
                $files = new DirectoryIterator($folder."/resources/db/schema");
                foreach($files as $file) {
                    if(!$file->isDot()) {
                        $table_name = str_replace(".php", "", basename($file->getFilename()));
                        # Higher schema should override.
                        $this->_schemaFiles[$table_name] = $folder."/resources/db/schema/".$file->getFilename();
                    }
                }
            }
        }

        foreach($editions as $edition) {
            $folder = str_replace("/sae/", "/$edition/", $basePath);
            if(is_readable($folder."/resources/db/data")) {
                $files = new DirectoryIterator($folder."/resources/db/data");
                foreach($files as $file) {

                    /** Database & Template updates */
                    if(preg_match("/^(.*)\.php$/", $file->getFilename())) {
                        $this->_dbFiles[] = $file->getPathName();
                    }
                }
            }

        }

        $this->_lastVersion = $package_info["version"];
    }

    protected function _run($file) {

        try {
            $this->getTable()->install($this->getName(), $file);
        }
        catch(Exception $e) {
            $logger = Zend_Registry::get("logger");
            $logger->sendException("Fatal Error When Connecting to The Database: \n".print_r($e, true));
        }
        return $this;
    }

    protected function _installTemplate($file) {
        try {
            $this->getTable()->install("", $file, "");
        }
        catch(Exception $e) {
            unlink($file);
            $message = "
                Fatal Error When installing the Template (".$file.")
                File removed.
                ".print_r($e, true)."";

            $logger = Zend_Registry::get("logger");
            $logger->sendException($message);
        }
        return $this;
    }

}
