<?php
namespace Fewlines\Core\Locale;

use Fewlines\Core\Csv\Csv;
use Fewlines\Core\Helper\PathHelper;
use Fewlines\Core\Helper\ArrayHelper;
use Fewlines\Core\Application\ProjectManager;

class Translator
{
    /**
     * @var string
     */
    const SUBPATH_SEPERATOR = '.';

    /**
     * The types which are supported
     *
     * IMPORTANT: Do not change the order of these
     * files. It's used to specify the function which
     * gets the translation string
     *
     * @var array
     */
    private static $translationTypes = array('php', 'csv');

    /**
     * All cached php translation files
     * to prevent multiple includes
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
        $project = ProjectManager::getActiveProject();
        $project = $project ? $project : ProjectManager::getDefaultProject();

        $pathParts = ArrayHelper::clean(explode(self::SUBPATH_SEPERATOR, $path));
        $localeDir = PathHelper::getRealPath(LOCALE_PATH . DR_SP . $project->getId()) . Locale::getKey();
        $entryPoint = '';
        $entryPointIndex = 0;

        // Check if path is empty
        if (empty($pathParts)) {
            return '';
        }

        // Get entry point file
        for ($i = 0, $len = count($pathParts); $i < $len; $i++) {
            $isFile = false;
            $localeDir = PathHelper::getRealPath($localeDir);

            for ($x = 0, $lenX = count(self::$translationTypes); $x < $lenX; $x++) {
                $fileExt = self::$translationTypes[$x];
                $pathPart = $pathParts[$i];
                $isFile = is_file($localeDir . $pathPart . '.' . $fileExt);

                // Escape loop if file was found
                if (true == $isFile) {
                    break;
                }
            }

            // Attach current "dir" to the localdir (next level)
            $localeDir.= $pathPart;

            // Excape loop if entry point was found
            if (true == $isFile) {
                $entryPointIndex = $i;
                $entryPoint = $localeDir . '.' . $fileExt;
                break;
            }
        }

        // if (true == empty($entryPoint)) {
        //     throw new Translator\Exception\EntryPointNotFoundException("No entry point (file) found for: " . (string) $path);
        // }

        $pathParts = array_slice($pathParts, $entryPointIndex + 1);
        $pathParts = ArrayHelper::clean($pathParts);

        // The default translation value
        $value = $path;

        /**
         * Operate with the key functions for different file types.
         * At this point we are handling valid values
         */

        switch ($fileExt) {
            case self::$translationTypes[0]:
                $value = self::getValueByKeyPHP($entryPoint, $pathParts);
                break;

            case self::$translationTypes[1]:
                $value = self::getValueByKeyCSV($entryPoint, $pathParts);
                break;
        }

        /**
         * If the value is a array and empty it doesn't
         * need to be returned. Return a empty string
         * instead
         */

        if (empty($value)) {
            return '';
        }
        else {
            return $value;
        }
    }

    /**
     * @param  string $file
     * @param  array  $parts
     * @return string|array
     */
    private static function getValueByKeyPHP($file, $parts) {
        $translation = self::getPhpFileArray($file);
        $content = $translation;

        if(false == empty($parts)) {
            // Get content by path
            for ($i = 0, $len = count($parts); $i < $len; $i++) {
                $part = $parts[$i];

                foreach ($translation as $key => $value) {
                    if ($key == $part) {
                        if (true == is_array($value)) {
                            $translation = $value;

                            // Add array if last key
                            if ($i == $len - 1) {
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
        if (true == array_key_exists($file, self::$phpFileCache)) {
            return self::$phpFileCache[$file];
        }

        $translation = include $file;

        if (false == is_array($translation)) {
            throw new Translator\Exception\NoTranslationArrayFoundException(
                'The file "' . (string) $file . '" does not contain a return as array'
            );
        }

        self::$phpFileCache[$file] = $translation;

        return $translation;
    }

    /**
     * @param  string $file
     * @param  array  $parts
     * @return string|array
     */
    private static function getValueByKeyCSV($file, $parts) {
        $key = implode(self::SUBPATH_SEPERATOR, $parts);
        $val = Csv::getValue($file, $key);

        return $val !== false ? $val : '';
    }
}
