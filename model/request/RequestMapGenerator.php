<?php

require(dirname(__FILE__)."/../util/addendum/annotations.php");

/**
 * This class analyses annotations in the controller folder of any
 * loaded package.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class RequestMapGenerator {

    /**
     * This method recusivily looks through the given package for controllers using
     * annotations and generates a request map file which is returned in the form
     * of a string
     * @param string $dir
     * @return string
     */
    private function buildDirectory($dir) {
        if (!is_dir($dir) || false === ($dh = opendir($dir))) {
            return;
        }

        //for each file in the directory
        while (($file = readdir($dh)) !== false) {
            //if it is something we want to ignore...
            if ($file == "test" || $file == "." || $file == ".."  || $file == ".svn") {
                continue;
            }
            //if it is a directory...
            else if (is_dir($dir."/".$file)) {
                $string .= $this->buildDirectory($dir."/".$file);
            }
            //if it is a .php file where the first letter is upper case...
            else if (substr ($file, -4) == ".php" && ucfirst($file) == $file) {
                $string .= $this->analyseClass(realpath($dir."/".$file));
            }
        }

        return $string;
    }

    /**
     * This method uses reflection to see if the given class uses annotations
     * to define a request handler. It returns a string that contains the
     * serialized Resource.
     * 
     * @param string $file
     * @return string
     */
    private function analyseClass($file) {
        $class = pathinfo($file , PATHINFO_FILENAME);
        try {
            $reflection = new ReflectionClass($class);
        }
        catch (Exception $ex) {
            return;
        }
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $string = "";

        foreach ($methods as $method) {
            $annotation = new ReflectionAnnotatedMethod($method->class, $method->name);

            //if it is a request handler
            if ($annotation->hasAnnotation("RequestName")) {
                $requestName = $annotation->getAnnotation("RequestName")->value;
                $mappedParams = $annotation->getAnnotation("RequestParams")->value == null ? array() : $annotation->getAnnotation("RequestParams")->value;
                $cacheLength = $annotation->getAnnotation("CacheLength")->value == null ? array() : $annotation->getAnnotation("CacheLength")->value;
                $authenticator = $annotation->getAnnotation("RequestAuthenticator")->value == null ? array() : $annotation->getAnnotation("RequestAuthenticator")->value;
                $viewType = $annotation->getAnnotation("ViewType")->value == null ? array() : $annotation->getAnnotation("ViewType")->value;
                $viewTemplate = $annotation->getAnnotation("ViewTemplate")->value == null ? array() : $annotation->getAnnotation("ViewTemplate")->value;
                $customParams = array();
                
                foreach ($annotation->getAllAnnotations("CustomParam") as $custom) {
                    $customParams[$custom->name] = $custom->value;
                }

                $resource = new Resource($requestName, 
                                         $method->class,
                                         $method->name,
                                         $mappedParams,
                                         $authenticator,
                                         $cacheLength,
                                         $viewType,
                                         $viewTemplate,
                                         null,
                                         $customParams);
                $string .= "Dispatcher::addResource(unserialize('".serialize($resource)."'));\n";
            }
        }

        return $string;
    }

    /**
     * Writes the request-map to the system temp folder.
     * @param string $filename
     * @param string $contents
     */
    private static function writeRequestMap($package, $contents) {
        $contents = "<?php\n\n// controllers in: {$package}\n{$contents}";

        $filename = str_replace("/", "_",realpath($package));
        $filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.$filename.".request-map.php";

        try {
            file_put_contents($filename, $contents);
        }
        catch (FrameEx $up) {
            $up->setMessage("Could not write to: ".$filename);
            throw $up;
        }
    }

    /**
     * Get all the loaded packages and rebuild their request maps
     */
    public static function buildAll() {
        $generator = new RequestMapGenerator();

        foreach (Factory::getLoadedPackages() as $package) {
            $contents = $generator->buildDirectory($package);
            self::writeRequestMap($package, $contents);
        }
    }

    /**
     * Build a single package
     * @param string $package
     */
    public static function build($package) {
        $generator = new RequestMapGenerator();
        $contents = $generator->buildDirectory($package);
        self::writeRequestMap($package, $contents);
    }
}

