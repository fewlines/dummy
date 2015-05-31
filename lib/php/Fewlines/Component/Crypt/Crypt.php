<?php
namespace Fewlines\Component\Crypt;

class Crypt
{
    /**
     * A random key to optimize
     * the encryption of strings
     *
     * @var string
     */
    const KEY = '§!(=$&/)%ERWEKLÖ=)(")PÖY092=):YQT';

    /**
     * Hashes a string
     *
     * @param  string $str
     * @return string
     */
    public static function hash($str) {
        $chars = str_split($str);
        $hashmap = array();
        $securityKey = self::getKey();

        for ($i = 0; $i < count($chars); $i++) {
            $hashmap[] = md5(sha1($chars[$i] . $i * 42 . md5($securityKey)));
        }

        $charSet = ceil($hashmap[0] / count($hashmap));
        $convHashmap = array();

        for ($i = 0; $i < count($hashmap); $i++) {
            $convHashmap[] = substr($hashmap[$i], 0, $charSet);
        }

        $hashString = md5(sha1(implode($securityKey, $convHashmap)));

        return $hashString;
    }

    /**
     * Returns the key defined by user
     * or the own key
     *
     * @return string
     */
    private static function getKey() {
        return defined(SECURITY_KEY) ? SECURITY_KEY : self::KEY;
    }

    /**
     * Returns the iv size
     *
     * @return integer
     */
    private static function getIvSize() {
        return mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    }

    /**
     * Encrypts the given string
     *
     * @param  string $str
     * @return string
     */
    public static function encrypt($str) {
        $key = pack('H*', self::getKey());
        $iv = mcrypt_create_iv(self::getIvSize(), MCRYPT_RAND);
        $secretTxt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC, $iv);

        return base64_encode($iv . $secretTxt) . ";" . self::hash(self::getKey());
    }

    /**
     * Decrypts the given string
     *
     * @param  string $str
     * @return string
     */
    public static function decrypt($str) {
        if (false == self::isCrypted($str)) {
            throw new Exception\InvalidStringToDecryptException("
				The string given was not encrypted with this key
			");
        }

        $key = pack('H*', self::getKey());
        $ivSize = self::getIvSize();
        $secretTxt = base64_decode($str);
        $ivDec = substr($secretTxt, 0, $ivSize);
        $secretTxt = substr($secretTxt, $ivSize);

        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $secretTxt, MCRYPT_MODE_CBC, $ivDec);
    }

    /**
     * Check if the string was encrypted
     *
     * @param  string $str
     * @return boolean
     */
    public static function isCrypted($str) {
        return (bool)preg_match('/;' . self::hash(self::getKey()) . '/', $str);
    }
}
