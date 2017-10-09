<?php

namespace FilePile\FilePileLaravel\Support\Files;

use FilePile\FilePileLaravel\Support\Files\Exceptions\FileException;

class FileWriter{

    public function create($filePath, $fileContent){
        $this->createFileWithDirectory($filePath);
        $this->addContent($filePath, $fileContent);
    }

    public function addContent($filePath, $fileContent){
        $file = fopen($filePath, "w");
        if(!$file){
            throw new FileException('Unable to open file: '.$filePath);
        }
        fwrite($file, $fileContent);
        fclose($file);
    }

    private function createFileWithDirectory($filePath) {
        $pathParts = explode(DIRECTORY_SEPARATOR, $filePath);
        $directory = '';
        foreach ($pathParts as $pathPart) {
            $directory .= DIRECTORY_SEPARATOR. $pathPart;

            if (!is_writable($directory)) {
                throw new FileException('It looks like the FilePile do not have permission to create your files, please check the path permissions: '.$directory);
            }

            if (!is_dir($directory) && strlen($directory) > 0 && strpos($directory, ".") == false) {
                mkdir($directory , 655);
            }elseif(!file_exists($directory) && strpos($directory, ".") !== false){
                touch($directory);
            } 
        }
    }

}