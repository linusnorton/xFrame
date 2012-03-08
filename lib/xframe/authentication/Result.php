<?php
namespace xframe\authentication;

/**
 * Class to contain the result of an authentication
 */
class Result {

    const SUCCESS = 1;
    const NOT_INITILISED = 0;
    const GENERAL_FAILURE = -1;
    const IDENTITY_NOT_FOUND = -2;
    const INVALID_CREDENTIAL = -3;
    const AMBIGUOUS_IDENTITY = -4;

    /**
     *
     * @var int
     */
    private $code;

    /**
     *
     * @var array
     */
    private $message;

    /**
     * @param int $code
     * @param array $message
     */
    public function __construct($code = self::NOT_INITILISED, $message = array()) {
        $this->code = $code;
        $this->message = $messafge;
    }

    /**
     * @param int $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param array $message
     */
    public function setMessages($message) {
        $this->message = $message;
    }

    /**
     * @param string $message
     */
    public function addMessage($message) {
        $this->message[] = $message;
    }

    /**
     * @return array
     */
    public function getMessages() {
        return $this->message;
    }

    /**
     * True if the code is success
     * @return boolean
     */
    public function isValid() {
        return $this->code === self::SUCCESS;
    }

}

