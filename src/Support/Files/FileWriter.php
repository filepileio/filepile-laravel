<?php

namespace FilePile\FilePileLaravel\Support\Files;

use FilePile\FilePileLaravel\Support\Files\Exceptions\FileException;

class FileWriter
{
    /**
     * Contains all string with directory names, 
     * this is used as array so we can create each directory one at a time.
     *
     * @var array
     */
    private $path;

    /**
     * Store the file name
     *
     * @var string
     */
    private $fileName;

    /**
     * Separate the directory names from the file name.
     *
     * @param string $filePath
     * @param string $fileName
     */
    public function __construct($filePath, $fileName = null) {
        $pathParts = explode(DIRECTORY_SEPARATOR, $filePath);

        $this->path = array_filter($pathParts, function($key) {
            return !strpos($key, ".") && !empty($key);
        });

        $this->fileName = ($fileName != null) ? $fileName : end($pathParts);
    }

    /**
     * Include the content from file inside the file created on given path
     *
     * @param string $filePath
     * @param string $fileContent
     * @return void
     */
    public function addContent($fileContent) {
        $stringPath = implode(DIRECTORY_SEPARATOR, $this->path);
        $file = fopen($stringPath . DIRECTORY_SEPARATOR . $this->fileName, "w");

        if(!$file){
            throw new FileException('Unable to open file: '.$filePath);
        }

        fwrite($file, $fileContent);
        fclose($file);
    }

    /**
     * Create all directories from FilePile starting from base path
     * 
     * @return boolean
     */
    public function createDirectories() {
        $directory = base_path();
        $filePileDirectory = $directory . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $this->path);

        if (!is_writeable($directory)) {
            throw new FileException('It looks like the FilePile do not have permission to create your files, please check the path permissions: '.$directory);
        }

        if (!is_dir($filePileDirectory)) {
            mkdir($filePileDirectory, 655, true);
        }

        return true;
    }

    /**
     * Create the file inside the directories
     * 
     * @return boolean
     */
    public function createFile() {
        $stringPath = implode(DIRECTORY_SEPARATOR, $this->path);
        $fullPath = $stringPath . DIRECTORY_SEPARATOR . $this->fileName;

        if (!is_writeable($stringPath)) {
            throw new FileException('It looks like the FilePile do not have permission to create your files, please check the path permissions');
        }

        if (!file_exists($fullPath)) {
            touch($fullPath);
        }

        return true;
    }
}