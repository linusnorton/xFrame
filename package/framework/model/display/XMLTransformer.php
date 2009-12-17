<?php
/**
 * An XMLTransformer takes a Traversable object and transforms it into an XML
 * string
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
interface XMLTransformer {

    public function getXML(Transformable $object);

}
