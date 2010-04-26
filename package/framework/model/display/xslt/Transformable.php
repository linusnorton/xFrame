<?php

/**
 * A Transformable object can be transformed into a string
 * @author Linus Norton <linusnorton@gmail.com>
 */
interface Transformable {

    public function getTransformer();

    public function setTransformer(XMLTransformer $transformer);

}
