<?php

namespace igun997\Core;
use PhpZip\ZipFile;

class ZipPDF {
    public $input = NULL;
    public $output = NULL;
    public $temp = NULL;
    public static function log($debug){
        if (is_object($debug)){
            var_dump($debug).PHP_EOL;
        }elseif (is_array($debug)){
            var_dump($debug).PHP_EOL;
        }elseif ($debug != ""){
            echo $debug.PHP_EOL;
        }
    }

    public function zip($input,$output,$temp_path = "/etc/temp"){
        if (!is_dir($temp_path)){
            mkdir($temp_path);
        }
        $path = $temp_path;
        $zip = new ZipFile();
        try {
            $zip->openFile($input);
        }catch (PhpZip\Exception\ZipException $e){
            self::log("File Exist :".$output);
        }
        $res = $zip->getListFiles();
        $zip->extractTo($path);
        $img_path = [];
        foreach ($res as $index => $re) {
            $repath = $path."/".$re;
            $img_path[] = $repath;
        }

        self::log("Generating . . . ");

        try {
            $pdf = new Imagick($img_path);
            $pdf->setImageFormat('pdf');
            $pdf->writeImages($output, true);
            self::log("Generated !!");
            self::log("Path = ".$output);

        }catch (\ImagickException $exception){
            self::log("Passing ");
        }

        return $this;
    }

    public function merger($dir_pdfs,$output){
        $files = [];
        $dir = scandir($dir_pdfs,0);
        natsort($dir);
        foreach ($dir as $r ){
            if (count(explode('.',$r)) == 2 && $r != "."){
                $files[] = $dir_pdfs.$r;
            }
        }
        foreach ($files as $index => $file) {
            echo $file.PHP_EOL;
        }
        $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile={$output}/all.pdf"." ".implode(" ", $files);
        echo shell_exec($cmd);
    }
}