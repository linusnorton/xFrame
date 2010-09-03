<?php

/**
 * @author Linus Norton <linusnorton@gmail.com?
 * @package database
 *
 * This exception is thrown when Record->save() is called and 
 * the record contains cyclical constraints that cannot be resolvedd
 * in this instance you should override save implement a little bit of
 * clean up in the child's save method and call the parent::save();
 */
class CyclicalRelationshipException extends FrameEx {


}

