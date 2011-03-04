<?php

namespace xframe\exception;
use \SplSubject;
use \SplObserver;
use \Exception;

/**
 * Uses the observer pattern to handle exceptions. You may add a listener
 * by calling the attach method and passing an SplObserver.
 *
 * This code was largely inspired by the devzone article:
 *
 * http://devzone.zend.com/article/12229
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package exception
 */
class ExceptionHandler implements SplSubject {

    /**
     * @var array $observers list of observers to notify
     */
    private $observers;

    /**
     * @var array $exceptions
     */
    private $exceptions;

    /**
     * Set the initial state
     */
    public function __construct() {
        $this->observers = array();
        $this->exceptions = array();
    }

    /**
     * Attaches the given observer to the list of observers to be notified
     * when an exception occurs
     *
     * @param SplObserver $observer
     */
    public function attach(SplObserver $observer) {
        $id = spl_object_hash($observer);
        $this->observers[$id] = $observer;
    }

    /**
     * Detaches the given observer
     *
     * @param SplObserver $observer
     */
    public function detach(SplObserver $observer) {
        $id = spl_object_hash($observer);
        unset($this->observers[$id]);
    }

    /**
     * Notify the observers that the event as occured
     */
    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Register this exception handler with the SPL internals
     */
    public function register() { 
        set_exception_handler(array($this, 'handle'));
    }

    /**
     * Exception handler. Notifies all observers that an exception has occured
     * @param Exception $e
     */
    public function handle(Exception $e) {
        $this->exceptions[] = $e;
        $this->notify();
    }

    /**
     * Return the exceptions that has occured so far
     * @return array
     */
    public function getExceptions() {
        return $this->exceptions;
    }

    /**
     * Returns the last excepton that was thrown
     * @return Exception
     */
    public function getLastException() {
        return end($this->exceptions);
    }

    /**
     * @return array
     */
    public function getObservers() {
        return $this->observers;
    }

}