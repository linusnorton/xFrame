<?php

namespace xframe\request\annotation;
use \Annotation;

class Parameter extends Annotation { 
    
    public $name;
    public $validator;
    public $required = false;
    public $default;
}
