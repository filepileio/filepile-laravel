<?php

namespace FilePile\FilePileLaravel\Support\Version;

class ApplicationVersionChecker
{

    public static function get($composerLockPath=null)
    {
        $composerLockJSON = static::getComposerLockJSON($composerLockPath);
        if(!$composerLockJSON){
            return null;
        }
        $package = static::getPackageFromComposerLockJSON($composerLockJSON,'filepile/filepile-laravel');
        if(!$package){
            return null;
        }
        $output = $package->version.' ['.$package->source->reference.']';
        return $output;
    }

    private static function getComposerLockJSON($composerLockPath=null){
        if($composerLockPath == null){
            $composerLockPath = base_path('composer.lock');
        }
        if(file_exists($composerLockPath)){
            $content = file_get_contents($composerLockPath);
            if(!empty($content)){
                return json_decode($content);
            }
        }
        return null;
    }

    private static function getPackageFromComposerLockJSON($composerLockJSON, $packageName){
        foreach($composerLockJSON->packages as $package){
            if($package->name == $packageName){
                return $package;
            }
        }
        return null;
    }

}