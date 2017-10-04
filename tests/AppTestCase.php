<?php

namespace FilePile\FilePileLaravel\Tests;

use PHPUnit\Framework\TestCase;

class AppTestCase extends TestCase{

    public function createTemporaryFile($contents = null){
        $filePointer = tmpfile();
        fwrite($filePointer, $contents);
        return $filePointer;
    }

    public function getTemporaryFilePath($filePointer){
        $fileMeta = stream_get_meta_data($filePointer);
        return $fileMeta['uri'];
    }

}