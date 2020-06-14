<?php
require "vendor/autoload.php";

use PhpZip\ZipFile;

$rmdir = function($dir) {
    foreach(scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
    }
    rmdir($dir);
};
$debug = function ($debug){
    if (is_object($debug)){
        var_dump($debug).PHP_EOL;
    }elseif (is_array($debug)){
        var_dump($debug).PHP_EOL;
    }elseif ($debug != ""){
        echo $debug.PHP_EOL;
    }
};
$zipPdf = function ($input,$output,$temp_path = "temp"){
    $path = $temp_path;
    $debug = function ($debug){
        if (is_object($debug)){
            var_dump($debug).PHP_EOL;
        }elseif (is_array($debug)){
            var_dump($debug).PHP_EOL;
        }elseif ($debug != ""){
            echo $debug.PHP_EOL;
        }
    };
    $zip = new ZipFile();
    try {
        $zip->openFile($input);
    }catch (PhpZip\Exception\ZipException $e){
        $debug("File Exist :".$output);
    }
    $res = $zip->getListFiles();
    $zip->extractTo($path);
    $img_path = [];
    foreach ($res as $index => $re) {
        $repath = $path."/".$re;
        $img_path[] = $repath;
    }

    $debug("Generating . . . ");
//    if (file_exists($output)){
//        $debug("File Exist : ".$output);
//        $debug("Unlinking . . .");
//        unlink($output);
//        $debug("Unlinked");
//    }
    try {
        $pdf = new Imagick($img_path);
        $pdf->setImageFormat('pdf');
        $pdf->writeImages($output, true);
        $debug("Generated !!");
        $debug("Path = ".$output);

    }catch (\ImagickException $exception){
        $debug("Passing ");
    }
};

if (isset($argv[1])){
    $input = $argv[1];
    $debug("Set Input : ".$input);
    if (isset($argv[2])){
        $output =  $argv[2];
        $debug("Set Output : ".$output);

        if (is_dir($input) && is_dir($output)){
            $scandirs = scandir($input);
            if (count($scandirs) > 0){
                unset($scandirs[0]);
                unset($scandirs[1]);
                $max = count($scandirs);
                $debug("Total File : ".$max);
                $debug("--------------------------------");
                $progress = 0;
                foreach ($scandirs as $index => $scandir) {
                    $debug("Progress (".($progress++)."/$max)");
                    $debug("--------------------------------");
                    $session = time();
                    $path = $session;
                    mkdir($path,0777);
                    $path_dir = $input."/".$scandir;
                    $plain = explode(".",$scandir);
                    $debug($path_dir);
                    $zipPdf($path_dir,$output."/".$plain[0].".pdf",$path);
                    $rmdir($path);
                }
            }else{
                $debug("Input is Empty Directory");
            }

       }else{
           $debug("Invalid Directory");
       }


    }else{
        $debug("Output Directory Empty");
    }
}else{
    $debug("Input Directory Empty");
}
