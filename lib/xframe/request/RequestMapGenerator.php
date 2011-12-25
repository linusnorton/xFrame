<?php

namespace xframe\request;

use \ReflectionClass;
use \Exception;
use \ReflectionMethod;
use \ReflectionAnnotatedMethod;
use \Addendum;
use xframe\core\DependencyInjectionContainer;

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
     * @var DependencyInjectionContainer
     */
    private $dic;

    public function __construct(DependencyInjectionContainer $dic) {
        $this->dic = $dic;
        
        $this->includeDirs = array(
            $dic->root."src".DIRECTORY_SEPARATOR,
            $dic->root."lib".DIRECTORY_SEPARATOR
        );
        
        //include addendum
        require_once $dic->root."lib/addendum/annotations.php";

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
                $mappedParams = $this->getOrReturn($annotation, "Params", array());
                $cacheLength =  $this->getOrReturn($annotation, "CacheLength", false);
                $filter =  $this->getOrReturn($annotation, "Prefilter", null);
                $view =  $this->getOrReturn($annotation, "View", $this->dic->registry->get("DEFAULT_VIEW"));
                $template = $this->getOrReturn($annotation, "Template", $request);
                $customParams = array();

                foreach ($annotation->getAllAnnotations("CustomParam") as $custom) {
                    $customParams[$custom->name] = $custom->value;
                }

                $newLine = PHP_EOL.'    ';
                $string = "<?php".PHP_EOL.PHP_EOL;
                $string .= "// Automatically generated code, do not edit.".PHP_EOL;
                $string .= "return new {$method->class}({$newLine}";
                $string .= "\$this->dic,{$newLine}";
                $string .= "\$request,{$newLine}";
                $string .= var_export($method->name, true).",{$newLine}";
                $string .= "new {$view}(\$this->dic->registry, \$this->dic->root, ".var_export($template, true).", \$request->debug),{$newLine}";
                $string .= str_replace(PHP_EOL, "", var_export($mappedParams, true)).",{$newLine}";
                $string .= var_export($filter, true).",{$newLine}";
                $string .= var_export($cacheLength, true). PHP_EOL. ");";
                
                file_put_contents($this->dic->tmp.$request.".php", $string);
            }
        }
    }
    
    /**
     * Return the given parameter if it exists or the $default if not
     * @param ReflectionAnnotatedMethod $annotation
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    private function getOrReturn(ReflectionAnnotatedMethod $annotation, $param, $default) {
        return $annotation->hasAnnotation($param) ? $annotation->getAnnotation($param)->value : $default;
    }
}
