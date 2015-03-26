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
     * Get a translation from a file by a path
     *
     * @param  string|arary $path
     * @return string
     */
    public static function get($path) {
        if (false == is_array($path)) {
            $pathArray = explode(self::SUBPATH_SEPERATOR, $path);
            $pathArray = ArrayHelper::clean($pathArray);
        }

        if (false == is_array($path) && false == preg_match("/\./", $path) || true == is_array($path) && count($path) <= 1) {
            throw new Exception\InvalidPathException("
				Pleas enter a valid path to get a
				translated string (key needed)
			");
        }

        if (false == is_array($path)) {
            $subPath = $pathArray;
        }
        else {
            $subPath = $path;
        }

        $pathKey = array_pop($subPath);
        $fileName = array_pop($subPath) . "." . self::FILE_EXTENSION;

        $parts = array(array(LOCALE_PATH, self::$locale), $subPath);
        $parts = ArrayHelper::flatten($parts);

        $path = PathHelper::createPath($parts);
        $path.= $fileName;

        $val = Csv::getValue($path, $pathKey);

        if (true == empty($val)) {
            if (true == is_file($path)) {
                throw new Exception\EmptyOrInvalidValueException("
					No translation found (or empty) for
					\"" . $pathKey . "\" in \"" . $path . "\"
				");
            }
            else {
                throw new Exception\EmptyOrInvalidValueException("
					No translation found for \"" . $pathKey . "\"
				");
            }
        }

        return $val;
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
