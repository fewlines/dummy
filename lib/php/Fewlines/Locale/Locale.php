<?php
namespace Fewlines\Locale;

class Locale extends Translator
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
    private static $locale = 'en_EN';

    /**
     * Set the locale for the
     * path to look in
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
                self::$locale = self::en_EN;
                break;

            default:
                throw new Exception\LocaleKeyNotFoundException('The key "' . (string)$locale . '" was not found');
        }
    }

    /**
     * Returns the current
     * location key
     *
     * @return string
     */
    public static function getKey() {
        return self::$locale;
    }
}
