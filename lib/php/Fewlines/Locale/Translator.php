<?php
namespace Fewlines\Locale;

use Fewlines\Csv\Csv;
use Fewlines\Helper\PathHelper;
use Fewlines\Helper\ArrayHelper;

class Translator
{
	/**
     * @var string
     */
    const SUBPATH_SEPERATOR = '.';

	/**
     * The type which are supported
     *
     * IMPORTANT: Do not change the order of these
     * files. It's use to specify the function to
     * get the translation string
     *
     * @var array
     */
    private static $translationTypes = array('php', 'csv');

    /**
     * All cached php translation files
     * to preven multiple includes
     *
     * @var array
     */
    private static $phpFileCache = array();

    /**
     * Get a translation from a file by a path
     *
     * @param  string|arary $path
     * @return string
     */
    public static function get($path) {
        $pathParts = explode(self::SUBPATH_SEPERATOR, $path);
        $localeDir = PathHelper::getRealPath(LOCALE_PATH) . Locale::getKey();
        $entryPoint = '';
        $entryPointIndex = 0;

        // Get entry point file
        for($i = 0, $len = count($pathParts); $i < $len; $i++) {
            $isFile = false;
            $localeDir = PathHelper::getRealPath($localeDir);

            for($x = 0, $lenX = count(self::$translationTypes); $x < $lenX; $x++) {
                $fileExt = self::$translationTypes[$x];
                $pathPart = $pathParts[$i];
                $isFile = is_file($localeDir . $pathPart . '.' . $fileExt);

                // Escape loop if file was found
                if(true == $isFile) {
                    break;
                }
            }

            // Attach current "dir" to the localdir (next level)
            $localeDir.= $pathPart;

            // Return if entry point was found
            if(true == $isFile) {
                $entryPointIndex = $i;
                $entryPoint = $localeDir . '.' . $fileExt;
                break;
            }
        }

        if(true == empty($entryPoint)) {
            // Throw error (no entry point file was found)
        }

        $pathParts = array_slice($pathParts, $entryPointIndex+1);
        $pathParts = ArrayHelper::clean($pathParts);

        if(count($pathParts) === 0) {
            // Throw error (if no key is set)
        }

        // The translation value
        $value = '';

        /**
         * Operate the key function for different file types
         * At this point we are operating with valid values
         */

        switch($fileExt) {
            case self::$translationTypes[0]:
                $value = self::getValueByKeyPHP($entryPoint, $pathParts);
                break;

            case self::$translationTypes[1]:
                $value = self::getValueByKeyCSV($entryPoint, $pathParts);
                break;
        }

        // @todo: write log if value is empty

        return $value;
    }

    /**
     * @param  string $file
     * @param  array  $parts
     * @return string|array
     */
    private static function getValueByKeyPHP($file, $parts) {
        $translation = self::getPhpFileArray($file);
        $content = '';

        // Get content by path
        for($i = 0, $len = count($parts); $i < $len; $i++) {
            $part = $parts[$i];

            foreach($translation as $key => $value) {
                if($key == $part) {
                    if(true == is_array($value)) {
                        $translation = $value;

                        // Add array if last key
                        if($i == $len-1) {
                            $content = $value;
                        }
                    }
                    else {
                        $content = $value;
                    }

                    break;
                }
            }
        }

        return $content;
    }

    /**
     * Gets the php files array
     * using a cache
     *
     * @param  string $file
     * @return array
     */
    private static function getPhpFileArray($file) {
    	if(true == array_key_exists($file, self::$phpFileCache)) {
    		return self::$phpFileCache[$file];
    	}

    	$translation = include $file;

		if(false == is_array($translation)) {
            // Throw error (Translation file does not contain an array)
        }

    	self::$phpFileCache[$file] = $translation;

    	return $translation;
    }

    /**
     * @param  string $file
     * @param  array  $parts
     * @return string
     */
    private static function getValueByKeyCSV($file, $parts) {
        $key = implode(self::SUBPATH_SEPERATOR, $parts);
        $val = Csv::getValue($file, $key);

        return $val !== false ? $val : '';
    }
}