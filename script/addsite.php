<?php

/**
 * This script should run after the pear install.
 *
 * It asks the user whether they want to setup a website now
 */

//if (readln("Would you like to add a website now? [y/n] ") != "y") {
//    die();
//}

echo "\nIntro text\n\n";

$name = readln("Website name:", "example.com");
$path = readln("Website path:", "/var/www");
$virtualHostPath = readln("Path to virtual hosts: ","/etc/apache2/sites-available");
$hostsFilePath = readln("Path to hosts file:", "/etc/hosts");
$configDir = ('@data_dir@' == '@'.'data_dir@') ? dirname(__FILE__).'/../' : '@data_dir@'.'/xframe/';
$configDir .= 'config/';

if (is_dir($path."/".$name)) {
    echo "\nDirectory: ".$path."/".$name." already exists, skipping intial directory creation...\n";
}
else {
    echo "\nCreating ".$path."/".$name."...";
    mkdir($path."/".$name);
    echo "\nCopying default application into directory...";
    $source = ('@php_dir@' == '@'.'php_dir@') ? dirname(__FILE__).'/../app' : '@php_dir@'.'/xframe/app';
    smartCopy($source, $path."/".$name);
}

if (file_exists($configDir.$name.".conf")) {
    echo "\nConfiguration file for {$name} already exists, skipping creation...";
}
else {
    echo "\nCopying default configuration file...";
    $configFile = file_get_contents($configDir."default.conf");
    $configFile = str_replace("/var/www/app", $path."/".$name, $configFile);
    file_put_contents($configDir.$name.".conf", $configFile);
}

if (file_exists($virtualHostPath."/".$name)) {
    echo "\nVirtual host configuration already exists, skipping...";
}
else {
    echo "\nCreating virtual host configuration...";
    file_put_contents($virtualHostPath."/".$name, createVirtualHost($name, $path));
}

$hostsFile = file_get_contents($hostsFilePath);
$hostsEntry = "127.0.0.1       ".$name." resource.".$name;

if (strpos($hostsFile, $hostsEntry) === false) {
    echo "\nAdding hosts entry...";
    $hostsFile .= "\n".$hostsEntry;
    file_put_contents($hostsFilePath, $hostsFile);
}
else {
    echo "\nHosts entry already exists, skipping...";
}


echo "
Website setup complete.

\033[01;31mPlease restart apache\033[0m and then visit {$name} in your browser to confirm setup was successful.

Your configuration file is \033[01;31m{$configDir}{$name}.conf\033[0m please enter in your database credentials if required.\n\n";

function readln($prompt, $default = null) {
    $response = "";
    if ($default == null) {
        while ($response == "") {
            $response = readline($prompt);
        }
    }
    else {
        $response = readline($prompt." [".$default."] ");

        if ($response == "") {
            $response = $default;
        }
    }

    return $response;
}

function createVirtualHost($name, $path) {
    $webroot = ('@php_dir@' == '@'.'php_dir@') ? dirname(__FILE__).'/../www' : '@php_dir@'.'/xframe/www';
    return '
#CONFIG FOR WEBSITE
<VirtualHost *:80>
        ServerName '.$name.'

        DocumentRoot "'.$webroot.'"
        <Directory "'.$webroot.'">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        Alias "/resource" "'.$path.'/'.$name.'/resource"
        <Directory "'.$path.'/'.$name.'/resource">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
        </Directory>

        RewriteEngine on
        RewriteOptions inherit
</VirtualHost>

#CONFIG FOR RESOURCES
<VirtualHost *:80>
        ServerName resource.'.$name.'

        DocumentRoot "'.$path.'/'.$name.'/resource"
        <Directory "'.$path.'/'.$name.'/resource">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
        </Directory>

</VirtualHost>
        ';
}

function smartCopy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755)) {
    $result=false;

    if (is_file($source)) {
        if ($dest[strlen($dest)-1]=='/') {
            if (!file_exists($dest)) {
                cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
            }
            $__dest=$dest."/".basename($source);
        } else {
            $__dest=$dest;
        }
        $result=copy($source, $__dest);
        chmod($__dest,$options['filePermission']);

    } elseif(is_dir($source)) {
        if ($dest[strlen($dest)-1]=='/') {
            if ($source[strlen($source)-1]=='/') {
                //Copy only contents
            } else {
                //Change parent itself and its contents
                $dest=$dest.basename($source);
                @mkdir($dest);
                chmod($dest,$options['filePermission']);
            }
        } else {
            if ($source[strlen($source)-1]=='/') {
                //Copy parent directory with new name and all its content
                @mkdir($dest,$options['folderPermission']);
                chmod($dest,$options['filePermission']);
            } else {
                //Copy parent directory with new name and all its content
                @mkdir($dest,$options['folderPermission']);
                chmod($dest,$options['filePermission']);
            }
        }

        $dirHandle=opendir($source);
        while($file=readdir($dirHandle))
        {
            if($file!="." && $file!="..")
            {
                 if(!is_dir($source."/".$file)) {
                    $__dest=$dest."/".$file;
                } else {
                    $__dest=$dest."/".$file;
                }
                //echo "$source/$file ||| $__dest<br />";
                $result=smartCopy($source."/".$file, $__dest, $options);
            }
        }
        closedir($dirHandle);

    } else {
        $result=false;
    }
    return $result;
}