<?php
$files = [];
$dir = scandir("/home/indra/Documents/pdf",0);
natsort($dir);
foreach ($dir as $r ){
    if (count(explode('.',$r)) == 2 && $r != "."){
        $files[] = "/home/indra/Documents/pdf/".$r;
    }
}
foreach ($files as $index => $file) {
    echo $file.PHP_EOL;
}
$cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=/home/indra/Documents/pdf/all.pdf"." ".implode(" ", $files);
echo shell_exec($cmd);