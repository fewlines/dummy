<?php
namespace Fewlines\Locale;

use Fewlines\Csv\Csv;
use Fewlines\Helper\PathHelper;
use Fewlines\Helper\ArrayHelper;

class Locale
{
    /**
     * @var string
     */
    const de_DE = 'de_DE';

    /**
     * @var string
     */
    const en_EN = 'en_EN';

    /**
     * @var string
     */
    const SUBPATH_SEPERATOR = '.';

    /**
     * @var string
     */
    const FILE_EXTENSION = "csv";

    /**
     * @var string
     */
    private static $locale = 'en_EN';

    /**
     * The type which are supported
     *
     * IMPORTANT: Do not change the order of these
     * files. It's use to specify the function to
     * get the translation string
     *
     * @var array
     */
    private static $localeTypes = array('php', 'csv');

    /**
     * Get a translation from a file by a path
     *
     * @param  string|arary $path
     * @return string
     */
    public static function get($path) {
   //      pr($path);

   //      if (false == is_array($path)) {
   //          $pathArray = explode(self::SUBPATH_SEPERATOR, $path);
   //          $pathArray = ArrayHelper::clean($pathArray);
   //      }

   //      if (false == is_array($path) && false == preg_match("/\./", $path) || true == is_array($path) && count($path) <= 1) {
   //          throw new Exception\InvalidPathException("
			// 	Pleas enter a valid path to get a
			// 	translated string (key needed)
			// ");
   //      }

   //      if (false == is_array($path)) {
   //          $subPath = $pathArray;
   //      }
   //      else {
   //          $subPath = $path;
   //      }

   //      $pathKey = array_pop($subPath);
   //      $fileName = array_pop($subPath) . "." . self::FILE_EXTENSION;

   //      $parts = array(array(LOCALE_PATH, self::$locale), $subPath);
   //      $parts = ArrayHelper::flatten($parts);

   //      $path = PathHelper::createPath($parts);
   //      $path.= $fileName;

   //      $val = Csv::getValue($path, $pathKey);

       //      if (true == empty($val)) {
   //          if (true == is_file($path)) {
   //              throw new Exception\EmptyOrInvalidValueException("
			// 		No translation found (or empty) for
			// 		\"" . $pathKey . "\" in \"" . $path . "\"
			// 	");
   //          }
   //          else {
    //             throw new Exception\EmptyOrInvalidValueException("
				// 	No translation found for \"" . $pathKey . "\"
				// ");
    //         }
    //     }

    //     return $val;


        /**
         * V2 (std. String)
         */

        $pathParts = explode(self::SUBPATH_SEPERATOR, $path);
        $localeDir = PathHelper::getRealPath(LOCALE_PATH) . self::$locale;
        $entryPoint = '';
        $entryPointIndex = 0;

        // Get entry point file
        for($i = 0, $len = count($pathParts); $i < $len; $i++) {
            $isFile = false;
            $localeDir = PathHelper::getRealPath($localeDir);

            for($x = 0, $lenX = count(self::$localeTypes); $x < $lenX; $x++) {
                $fileExt = self::$localeTypes[$x];
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
            case self::$localeTypes[0]:
                $value = self::getValueByKeyPHP($entryPoint, $pathParts);
                break;

            case self::$localeTypes[1]:
                $value = self::getValueByKeyCSV($entryPoint, $pathParts);
                break;
        }

        return $value;
    }

    /**
     * @param  string $file
     * @param  array  $parts
     * @return string|array
     */
    private static function getValueByKeyPHP($file, $parts) {
        $translation = include $file;

        if(false == is_array($translation)) {
            // Throw error (Translation file does not contain an array)
        }

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
     * @param  string $file
     * @param  array  $parts
     * @return string
     */
    private static function getValueByKeyCSV($file, $parts) {
        $key = implode(self::SUBPATH_SEPERATOR, $parts);

        pr($key);

        // $val = Csv::getValue($path, $pathKey);
    }

    /**
     * Set the locale for the path to
     * look in
     *
     * @param string $locale
     */
    public static function set($locale) {
        switch ($locale) {
            case 'de':
            case 'deDE':
            case 'de_DE':
                self::$locale = self::de_DE;
                break;

            case 'en':
            case 'enEN':
            case 'en_EN':
            default:
                // @todo: add warning to log (locale not found)
                self::$locale = self::en_EN;
                break;
        }
    }

    /**
     * Returns the current location key
     *
     * @return string
     */
    public static function getKey() {
        return self::$locale;
    }
}
