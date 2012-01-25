<?php

namespace xframe\request;

use \ReflectionClass;
use \Exception;
use \ReflectionMethod;
use \ReflectionAnnotatedMethod;
use \Addendum;
use \xframe\core\DependencyInjectionContainer;

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

    /**
     * @param DependencyInjectionContainer $dic 
     */
    public function __construct(DependencyInjectionContainer $dic) {
        $this->dic = $dic;
        
        $this->includeDirs = array(
            $dic->root."src".DIRECTORY_SEPARATOR,
            $dic->root."lib".DIRECTORY_SEPARATOR
        );
        
        //include addendum
        require_once "addendum/annotations.php";

        Addendum::ignore("Entity");
        Addendum::ignore("MappedSuperclass");
        Addendum::setClassnames(
            array(
                "Request" => "xframe\\request\\annotation\\Request",
                "View" => "xframe\\request\\annotation\\View",
                "Prefilter" => "xframe\\request\\annotation\\Prefilter",
                "CacheLength" => "xframe\\request\\annotation\\CacheLength",
                "CustomParam" => "xframe\\request\\annotation\\CustomParam",
                "Parameter" => "xframe\\request\\annotation\\Parameter",
                "Template" => "xframe\\request\\annotation\\Template"
            )
        );
        
        // make sure we can write to the tmp directory or use the sys tmp
        if (!is_writable($this->dic->tmp)) {
            $this->dic->tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR;
        }
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
            if ($file == '.' || $file == '..' || $file == '.svn') {
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
                $this->processRequest($annotation);
            }     
        }
    }
    
    /**
     * Create the cache file for the request
     * 
     * @param ReflectionAnnotatedMethod $annotation 
     */
    private function processRequest(ReflectionAnnotatedMethod $annotation) {
        $request = $annotation->getAnnotation("Request")->value;
        $mappedParams = $annotation->getAllAnnotations("Parameter");
        $cacheLength =  $this->getOrReturn($annotation, "CacheLength", false);
        $view =  $this->getOrReturn($annotation, "View", $this->dic->registry->get("DEFAULT_VIEW"));
        $template = $this->getOrReturn($annotation, "Template", $request);

        $prefilters = array();

        foreach ($annotation->getAllAnnotations("Prefilter") as $prefilter) {
            $prefilters[] = $prefilter->value;
        }

        $customParams = array();

        foreach ($annotation->getAllAnnotations("CustomParam") as $custom) {
            $customParams[$custom->name] = $custom->value;
        }

        $newLine = PHP_EOL.'    ';
        $fileContents = "<?php".PHP_EOL.PHP_EOL;
        $fileContents .= "// Automatically generated code, do not edit.".PHP_EOL;

        if (count($customParams) > 0) {
            $fileContents .= "\$request->addParameters(".var_export($customParams, true).");".PHP_EOL;
        }
        
        $fileContents .= "return new {$annotation->class}({$newLine}";
        $fileContents .= "\$this->dic,{$newLine}";
        $fileContents .= "\$request,{$newLine}";
        $fileContents .= var_export($annotation->name, true).",{$newLine}";
        $fileContents .= "new {$view}(\$this->dic->registry, \$this->dic->root, \$this->dic->tmp, ";
        $fileContents .= var_export($template, true).", \$request->debug),{$newLine}";
        $fileContents .= "array({$newLine}";
        
        foreach ($mappedParams as $param) {
            $fileContents .= 'new xframe\request\Parameter(\''.$param->name.'\',' . $newLine;
            $fileContents .= $param->validator ? 'new '.$param->validator.',' . $newLine : "null,{$newLine}";
            $fileContents .= var_export($param->required, true) . ",{$newLine}";
            $fileContents .= var_export($param->default, true) . "),";
        }
        $fileContents .= "),{$newLine}";
        $fileContents .= "array(";

        foreach ($prefilters as $filter) {
            $fileContents .= "new {$filter}, ";
        }

        $fileContents .= "),{$newLine}";
        $fileContents .= var_export($cacheLength, true). PHP_EOL. ");";

        $filename = $this->dic->tmp.$request.".php";

        try {
            file_put_contents($filename, $fileContents);
        }
        catch (Exception $e) {
            throw new Exception("Could not create request cache file: ".$filename, 0, $e);
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
