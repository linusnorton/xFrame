<?php

namespace xframe\autoloader;

/**
 * Builds the class map file for the given path
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package core
 */
class ClassMapBuilder {
    
    /**
     * @var string the root directory
     */
    private $root;

    /**
     * @var array of acceptable class file extensions
     */
    private $fileTypes;

    /**
     * @var array the classes and interfaces found under the root directory
     */
    private $classes;

    /**
     * @param string $root
     */
    public function __construct($root, $fileTypes = array("php")) {
        $this->root = $root;
        $this->fileTypes = $fileTypes;
        $this->classes = array();
    }

    /**
     * Recursively scans directories on the root directory for class files
     */
    public function build() {
        $this->scanDirectory($this->root."lib");
        $this->scanDirectory($this->root."src");
        $this->scanDirectory($this->root."test");
    }

    /**
     * Loads the given directory and scans it for php files
     * @param string $directory
     */
    private function scanDirectory($directory) {
        $dh = @opendir($directory);

        //if the directory could be opened
        if ($dh !== false) {
            //for each file
            while (($file = readdir($dh)) !== false) {
                if ($file == "." || $file == ".."  || $file == ".svn") {
                    continue;
                }

                $fullpath = $directory.DIRECTORY_SEPARATOR.$file;
                $ext = pathinfo($fullpath, PATHINFO_EXTENSION);

                //if the file is a directory scan the directory
                if (is_dir($fullpath)) {
                    $this->scanDirectory($fullpath);
                }
                //if it's an acceptable file type scan the file
                else if (in_array($ext, $this->fileTypes)){
                    $this->scanFile($fullpath);
                }
            }
        }

        closedir($dh);
    }

    /**
     * Scans the given file for classes and adds them to the class map
     * @param string $filename
     */
    private function scanFile($filename) {
        $contents = file_get_contents($filename);
        $tokens = token_get_all($contents);
        $classes = array();
        $filename = realpath($filename);
        $numTokens = count($tokens);
        
        for ($i = 2; $i < $numTokens; $i++) {
            $token = $tokens[$i];

             if (is_array($token)) {
                if ($token[0] == T_CLASS || $token[0] == T_INTERFACE) {
                    //get the class name
                    for ($j = $i + 1; $j < $numTokens; $j++) {
                        if ($tokens[$j][0] == T_STRING) {
                            $name .= $tokens[$j][1];
                            break;
                        }
                    }

                    $this->classes[$name] = $filename;
                    $name = "";
                    $i = $j;
                }
                else if ($token[0] == T_NAMESPACE) {
                    $name = "";
                    for ($j = $i + 1; $j < $numTokens; $j++) {
                        if ($tokens[$j] == ';') {
                            $i = $j;
                            break;
                        }
                        else if ($tokens[$j][0] == T_STRING) {
                            $name .= $tokens[$j][1].'\\';
                        }
                    }
                }
             }
        }
    }

    /**
     * Outputs the class map to the given file
     * @param string $filename
     */
    public function output($filename) {
        $contents = '<?php '.PHP_EOL.'$c = array();'.PHP_EOL;

        foreach ($this->classes as $class => $path) {
            $contents .= '$c["'.$class.'"] = "'.$path.'";'.PHP_EOL;
        }

        $contents .= 'return $c;'.PHP_EOL;
        file_put_contents($filename, $contents);
    }
}
