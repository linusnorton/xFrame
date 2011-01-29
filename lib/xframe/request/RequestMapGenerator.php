<?php

namespace xframe\request;

use \ReflectionClass;
use \Exception;
use \ReflectionMethod;
use \ReflectionAnnotatedMethod;
use \Addendum;
use xframe\core\System;

/**
 * This class analyses class annotations to create request map files for each
 * controller.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class RequestMapGenerator {
    /**
     * @var array
     */
    private $includeDirs;
    /**
     * @var System
     */
    private $system;

    public function __construct(System $system) {
        $this->system = $system;
        
        $this->includeDirs = array(
            $system->root."src".DIRECTORY_SEPARATOR,
            $system->root."lib".DIRECTORY_SEPARATOR
        );
        
        //include addendum
        require_once $system->root."lib/addendum/annotations.php";

        Addendum::ignore("Entity");
        Addendum::ignore("MappedSuperclass");
        Addendum::setClassnames(
            array(
                "Request" => "xframe\\request\\annotation\\Request",
                "View" => "xframe\\request\\annotation\\View",
                "Prefilter" => "xframe\\request\\annotation\\Prefilter",
                "CacheLength" => "xframe\\request\\annotation\\CacheLength",
                "CustomParam" => "xframe\\request\\annotation\\CustomParam",
                "Params" => "xframe\\request\\annotation\\Params",
                "Template" => "xframe\\request\\annotation\\Template"
            )
        );
    }

    /**
     * This method recursively looks through the given dir for controllers using
     * annotations and generates a request map file
     *
     * @param string $dir
     */
    public function scan($dir) {
        if (!is_dir($dir) || false === ($dh = opendir($dir))) {
            return;
        }

        //for each file in the directory
        while (($file = readdir($dh)) !== false) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            //if it is something we want to ignore...
            if ($file == '.' || $file == '..'  || $file == '.svn') {
                continue;
            }
            //if it is a directory...
            else if (is_dir($path)) {
                $this->scan($path);
            }
            //if it is a .php file 
            else if (substr($path, -4) == ".php") {
                $class = str_replace($this->includeDirs, '', $path);
                $class = str_replace(DIRECTORY_SEPARATOR, '\\', $class);
                $class = pathinfo($class , PATHINFO_FILENAME);

                $this->analyseClass($class);
            }
        }
    }

    /**
     * This method uses reflection to see if the given class uses annotations
     * to define a request handler. It returns a string that contains the
     * serialized Resource.
     *
     * @param string $file
     * @return string
     */
    private function analyseClass($class) {
        try {
            $reflection = new ReflectionClass($class);
        }
        catch (Exception $ex) {
            return;
        }

        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $annotation = new ReflectionAnnotatedMethod($method->class, $method->name);

            //if it is a request handler
            if ($annotation->hasAnnotation("Request")) {
                $request = $annotation->getAnnotation("Request")->value;
                $mappedParams = $annotation->getAnnotation("Params")->value == null ? array() : $annotation->getAnnotation("Params")->value;
                $cacheLength = $annotation->getAnnotation("CacheLength")->value == null ? false : $annotation->getAnnotation("CacheLength")->value;
                $filter = $annotation->getAnnotation("Prefilter")->value == null ? null : $annotation->getAnnotation("Prefilter")->value;
                $view = $annotation->getAnnotation("View")->value == null ? $this->system->registry->get("DEFAULT_VIEW") : $annotation->getAnnotation("View")->value;
                $template = $annotation->getAnnotation("Template")->value == null ? $request : $annotation->getAnnotation("Template")->value;
                $customParams = array();

                foreach ($annotation->getAllAnnotations("CustomParam") as $custom) {
                    $customParams[$custom->name] = $custom->value;
                }

                $string = "<?php\n\n";
                $string .= "return new {$method->class}(\$this->system,";
                $string .= "\$request,";
                $string .= var_export($method->name, true).", ";
                $string .= "new {$view}(\$this->system->registry, \$this->system->root, ".var_export($template, true).", \$request->debug), ";
                $string .= var_export($mappedParams, true).", ";
                $string .= var_export($filter, true).", ";
                $string .= var_export($cacheLength, true)." );";
                
                file_put_contents($this->system->tmp.$request.".php", $string);
            }
        }
    }
}
