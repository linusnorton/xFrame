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
class Logger implements SplObserver {

    /**
     * Log the exception.
     * 
     * @param SplSubject $subject 
     */
    public function update(SplSubject $subject) {
        error_log($subject->getLastException()->getMessage());
    }


}
