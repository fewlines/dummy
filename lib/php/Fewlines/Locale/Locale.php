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
    private static $locale = 'en_EN';

    /**
     * Set the locale for the
     * path to look in
     *
     * @param string $locale
     */
    public static function set($locale) {
        self::$locale = $locale;
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
