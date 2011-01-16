<?php

namespace xframe\request\annotation;
use \Annotation;

/**
 * @author Linus Norton <linusnorton@gmail.com>
 */
class CustomParam extends Annotation {
    public $name;
    public $value;
}
