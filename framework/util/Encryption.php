<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
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
        $iv_size = mcrypt_get_iv_size($this->algorithm, $this->mode);
        return mcrypt_create_iv($iv_size, $this->mode);
    }

    /**
     * encrypt the given string using the key and iv
     */
    public function encrypt($plainText, $key, $iv) {
        return bin2hex(mcrypt_encrypt($this->algorithm, $key, $plainText, $this->mode, pack("H*",$iv)));
    }

    /**
     * decrypt the given string using the key and iv
     */
    public function decrypt($encryptedText, $key, $iv) {
        return str_replace("\x0", '', mcrypt_decrypt( $this->algorithm , $key  , pack("H*",$encryptedText ) , $this->mode , pack("H*",$iv)));
    }

}

?>