<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package util
 *
 * Object wrapper for some mcrypt functions. This requires the mcrypt and mhash extentions
 */
class Encryption {
    private $algorithm;
    private $mode;

    /**
     * Construct the encryption algorithm and set the modes
     */
    public function __construct($algorithm = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC) {
        $this->algorithm = $algorithm;
        $this->mode = $mode;
    }

    /**
     * Create the initialization vector
     */
    public function createIV() {
        $ivSize = mcrypt_get_iv_size($this->algorithm, $this->mode);
        return mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM);
    }

    /**
     * encrypt the given string using the key and iv return the HEX string
     *
     * Use this method if you want to get a hex string back
     */
    public function encryptString($plainText, $key, $iv) {
        return bin2hex(mcrypt_encrypt($this->algorithm, $key, $plainText, $this->mode, $iv));
    }

    /**
     * decrypt the given HEX string using the key and iv
     *
     * Use this method if you are passing through a hex string
     */
    public function decryptString($encryptedText, $key, $iv) {
        $decryptedText = mcrypt_decrypt( $this->algorithm , $key  , pack("H*",$encryptedText ) , $this->mode , $iv);
        return str_replace("\x0", '', $decryptedText);
    }

    /**
     * encrypt the given string using the key and iv return the long
     */
    public function encrypt($plainText, $key, $iv) {
        return mcrypt_encrypt($this->algorithm, $key, $plainText, $this->mode, $iv);
    }

    /**
     * decrypt the given long using the key and iv
     */
    public function decrypt($encryptedText, $key, $iv) {
        $decryptedText = mcrypt_decrypt($this->algorithm , $key, $encryptedText , $this->mode, $iv);
        return str_replace("\x0", '', $decryptedText);
    }

}

