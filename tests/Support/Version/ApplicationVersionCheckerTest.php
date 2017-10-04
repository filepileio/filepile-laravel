<?php

namespace FilePile\FilePileLaravel\Tests\Support\Version;

use FilePile\FilePileLaravel\Tests\AppTestCase;

class ApplicationVersionCheckerTest extends AppTestCase
{

    public function test_show_based_on_composer_lock(){
        $composerLockContent = file_get_contents(dirname(__FILE__).'/../../stubs/composer.lock.stub');
        $composerLock = $this->createTemporaryFile($composerLockContent);
        $composerLockPath = $this->getTemporaryFilePath($composerLock);
        $version = \FilePile\FilePileLaravel\Support\Version\ApplicationVersionChecker::get($composerLockPath);
        $this->assertContains('dev-master', $version);
        $this->assertContains('79611d7b4fca064b82f8cadb60f73367b5493478', $version);
    }

}
