<?php

namespace xframe\cli;
use xframe\request\Controller;

/**
 * Endpoint for the xFrame CLI, creates a new xFrame project structure
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class CreateProject extends Controller {

    /**
     * @Request("create-project")
     * @Params({"path"});
     */
    public function run() {
        $this->view->destination = $this->request->path;
        // copy the entire folder
        $this->recursiveCopy($this->dic->root, $this->request->path);
        
        // remove xframe and rebuild
        $this->recursiveDelete($this->request->path.'/src/xframe');        
        $this->recursiveDelete($this->request->path.'/lib');
        $this->recursiveDelete($this->request->path.'/tmp');
        $this->recursiveDelete($this->request->path.'/log');
        
        mkdir($this->request->path.'/lib', 0755);
        mkdir($this->request->path.'/tmp');        
        mkdir($this->request->path.'/log');
    }
    
    /**
     * Taken from somewhere on http://php.net
     */
    private function recursiveCopy($source, $dest, $options = array('folderPermission' => 0755, 'filePermission' => 0755 )) {
        $result = false;

        if (is_file($source)) {
            if ($dest[strlen($dest)-1] == '/') {
                if (!file_exists($dest)) {
                    cmfcDirectory::makeAll($dest, $options['folderPermission'], true);
                }
                $__dest = $dest."/".basename($source);
            } 
            else {
                $__dest = $dest;
            }
            $result = copy($source, $__dest);
            chmod($__dest, $options['filePermission']);
        } 
        else if (is_dir($source)) {
            if ($dest[strlen($dest)-1] == '/') {
                if ($source[strlen($source)-1] == '/') {
                    //Copy only contents
                } 
                else {
                    //Change parent itself and its contents
                    $dest = $dest.basename($source);
                    @mkdir($dest);
                    chmod($dest,$options['filePermission']);
                }
            } 
            else {
                if ($source[strlen($source)-1] == '/') {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                } 
                else {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                }
            }

            $dirHandle = opendir($source);
            while ($file = readdir($dirHandle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($source."/".$file)) {
                        $__dest = $dest."/".$file;
                    } 
                    else {
                        $__dest = $dest."/".$file;
                    }
                    
                    $result = $this->recursiveCopy($source."/".$file, $__dest, $options);
                }
            }
            
            closedir($dirHandle);
        } 
        else {
            $result = false;
        }
        
        return $result;
    }    
    
    /**
     * Recursively delete a directory
     * @param string $dir 
     */
    function recursiveDelete($dir) { 
         if (is_dir($dir)) { 
             $objects = scandir($dir); 
             foreach ($objects as $object) { 
                 if ($object != "." && $object != "..") { 
                    if (filetype($dir."/".$object) == "dir") {                        
                        $this->recursiveDelete($dir."/".$object); 
                    }
                    else {
                        unlink($dir."/".$object); 
                    }
                 } 
             } 
             reset($objects); 
             rmdir($dir); 
         } 
     }     
}

