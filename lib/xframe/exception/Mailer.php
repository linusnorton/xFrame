<?php

namespace xframe\exception;
use \SplObserver;
use \SplSubject;

/**
 * Uses the observer pattern to listen for exceptions.
 *
 * This code was largely inspired by the devzone article:
 *
 * http://devzone.zend.com/article/12229
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package exception
 */
class Mailer implements SplObserver {

    /**
     * @var string $recipients
     */
    private $recipients;

    /**
     * @param string $recipients
     */
    public function __construct($recipients) {
        $this->recipients = $recipients;
    }

    /**
     * Mail the exception.
     *
     * @param SplSubject $subject
     */
    public function update(SplSubject $subject) {
        error_log($subject->getLastException()->getMessage());
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From: "'.$_SERVER["SERVER_NAME"]
                             .$_SERVER["REQUEST_URI"]
                             .'" <xframe@'.$_SERVER["SERVER_NAME"].">\r\n";
        $headers .= 'Content-type: text/plain; charset=iso-8859-1'."\r\n";
 
        mail($this->recipients,
             $subject->getLastException()->getMessage(),
             $subject->getLastException()->__toString(),
             $headers);
    }
}
