<?php

spl_autoload_register(function($className) {
     //list comma separated directory name
     $directory = array('', 'core/', 'models/', 'controllers/', "views/");

     //list of comma separated file format
     $fileFormat = array('%s.php');
 
     foreach ($directory as $current_dir) {
         foreach ($fileFormat as $current_format) {
 
             $path = ROOT.$current_dir . sprintf($current_format, $className);
             if (file_exists($path)) {
                 include $path;
                 return;
             }
         }
     }
});

?>