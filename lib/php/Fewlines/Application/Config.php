<?php
namespace Fewlines\Application;

use Fewlines\Helper\DirHelper;
use Fewlines\Helper\PathHelper;
use Fewlines\Helper\ArrayHelper;
use Fewlines\Xml\Xml;

class Config
{
    /**
     * Holds the instance
     *
     * @var \Fewlines\Application\Config
     */
    private static $instance;

    /**
     * Holds the paths to the config files
     *
     * @var array
     */
    private $configFiles = array();

    /**
     * Holds the path to the config files
     * which were loaded
     *
     * @var array
     */
    private $loadedConfigFiles = array();

    /**
     * The input of all files as an array
     *
     * @var array
     */
    private $xmls = array();

    /**
     * Load config files
     *
     * @param array $configs
     */
    public function __construct($configs) {
        if (false == is_null(self::$instance)) {
            throw new Exception\ConfigJustInstantiatedException("
					The config object has already been instantiated.
					Use the static function \"getInstance\" instead.
				");
        }

        // Add config files
        $this->addConfigFiles($configs);

        // Set instance for singletons
        self::$instance = $this;
    }

    /**
     * Returns the instane created
     *
     * @return \Fewlines\Application\Config
     */
    public static function getInstance() {
        if (true == is_null(self::$instance)) {
            self::$instance = new self(getConfig());
        }

        return self::$instance;
    }

    /**
     * Adds the config files to
     * the local config files var
     *
     * @param array $configs
     */
    public function addConfigFiles($configs) {
        $files = array();

        for ($i = 0, $len = count($configs); $i < $len; $i++) {
            $dir = PathHelper::normalizePath($configs[$i]['dir']);

            if ( ! is_dir($dir)) {
                continue;
            }

            $files[$dir] = DirHelper::getFilesByType($dir, $configs[$i]['type'], true);
        }

        $files = DirHelper::flattenTree($files);

        for ($i = 0, $len = count($files); $i < $len; $i++) {
            $this->configFiles[] = $files[$i];
        }

        // Reload config file list
        $this->updateFiles();
    }

    /**
     * Updates the current file list
     */
    private function updateFiles() {
        for ($i = 0; $i < count($this->configFiles); $i++) {
            // Check if config file was already loaded
            if (is_int(array_search($this->configFiles[$i], $this->loadedConfigFiles))) {
                continue;
            }

            $filePath = $this->configFiles[$i];
            $filename = basename($filePath);
            $ignore = preg_match("/^_(.*)$/", $filename);

            if ( ! $ignore) {
                $this->xmls[] = new Xml($filePath);
                $this->loadedConfigFiles[] = $filePath;
            }
        }

        // Check for replacement
        $this->checkReplacements();
    }

    /**
     * This will check for replacement flag the user is able
     * to set. This will prevent a merging of the childs
     * from a subtree and remove the one which is marked
     * as: replaceable="true"
     */
    private function checkReplacements() {
        $xmls = array();

        foreach ($this->xmls as $xml) {
            $name = $xml->getTreeElement()->getName();

            if ( ! array_key_exists($name, $xmls)) {
                $xmls[$name] = array();
            }

            $xmls[$name][] = $xml;
        }

        // Handle conflicts ...
        foreach ($xmls as $name => $xml) {
            // ... between 2 elements
            if (count($xml) == 2) {
                $affector = null;
                $replace = false;

                foreach ($xmls[$name] as $subXml) {
                    if (filter_var($subXml->getTreeElement()->getAttribute('replaceable'), FILTER_VALIDATE_BOOLEAN)) {
                        $affector = $subXml;
                    }
                    else if (filter_var($subXml->getTreeElement()->getAttribute('replace'), FILTER_VALIDATE_BOOLEAN)) {
                        $replace = true;
                    }
                }

                if ($replace) {
                    if ($affector) {
                        $index = array_search($affector, $this->xmls);

                        if (is_int($index)) {
                            // Delete from array and resort so the index won't mess up
                            unset($this->xmls[$index]);
                            ArrayHelper::clean($this->xmls);
                        }
                        else {
                            throw new Config\Exception\XmlTreeNotFoundException(
                                'The xml tree could not be found (for replacement)
                                in the config list'
                            );
                        }
                    }
                    else {
                        throw new Config\Exception\NoAffectedXmlTreeFoundException(
                            'No affected xml tree found to remove. Please use
                            replaceable="true" as attributes to define the replacement'
                        );
                    }
                }
            }
        }

    }

    /**
     * Gets elements by path
     * sequence (in all xml files)
     *
     * @param  string $path
     * @return array
     */
    public function getElementsByPath($path) {
        $elements = array();

        foreach ($this->xmls as $xml) {
            $result = $xml->getElementsByPath($path);

            if (false != $result) {
                $elements[] = $result;
            }
        }

        return ArrayHelper::flatten($elements);
    }

    /**
     * Gets a element by path
     * sequence (searched in all xml files)
     * Try to only use this if you know what
     * you will get (e.g. a single element)
     * Will Return the first result which is found.
     *
     * @param  string $path
     * @return \Fewlines\Xml\Tree\Element|boolean
     */
    public function getElementByPath($path) {
        foreach ($this->xmls as $xml) {
            $result = $xml->getElementByPath($path);

            if (false != $result) {
                return $result;
            }
        }

        return false;
    }
}
