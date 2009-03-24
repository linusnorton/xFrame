<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @version 0.1
 * @package display
 *
 * This interface should provide an object with the ability to return a view of itself in XML
 */
interface XML {

    /**
     * Return me as XML
     *
     * @return string XML 
     */
    public function getXML();

}