<?php

namespace FilePile\FilePileLaravel\Commands;

use FilePile\FilePileLaravel\Support\Files\Exceptions\FileException;
use FilePile\FilePileLaravel\Support\Files\FileWriter;
use Illuminate\Console\Command;

class FilePileInstallPile extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filepile:install {pileSlug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install selected pile';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if (config('app.env') == 'production' && !config('filepile.enableInProduction')) {
            $this->error('Error: Application is in production. You could set FILEPILE_ENABLE_PRODUCTION=true, but that would be madness');
            return null;
        }
        $pileSlug = $this->argument('pileSlug');
        $pile = $this->getPileFromSlug($pileSlug);
        if (!$pile) {
            $this->error('Pile not found!');
            return null;
        }
        $pileBaseInstallPath = $this->getPileBaseInstallPath($pile->install_path);
        $promptInputs = $this->getPromptInputs($pile);
        $files = $this->getFiles($pile, $promptInputs);
        if (!$files) {
            $this->error('The selected Pile contains an empty repository');
            return null;
        }
        $writeFilesConfirmation = $this->getWriteFilesConfirmation($pileBaseInstallPath, $files);
        if ($writeFilesConfirmation) {
            $this->installFiles($pileBaseInstallPath, $files);
            $this->showPostInstallationMessage($pile);
        }
    }

    private function getPileFromSlug($pileSlug) {
        $apiClient = new \FilePile\FilePileLaravel\API\Client();
        try {
            $pileResponse = $apiClient->call('GET', '/api/v1/account/pile/find', ['slug' => $pileSlug]);
        } catch (\FilePile\FilePileLaravel\API\Exceptions\InvalidKeyException $exception) {
            $this->error('Error: ' . $exception->getMessage());
            return null;
        }
        return json_decode($pileResponse);
    }

    private function getPileBaseInstallPath($defaultPath = '/') {
        if (empty($defaultPath)) {
            $defaultPath = '/';
        }
        return $this->ask('Pile Install Path (From Project Root)?', $defaultPath);
    }

    private function getPromptInputs($pile) {
        $apiClient = new \FilePile\FilePileLaravel\API\Client();
        $promptsResponse = $apiClient->call('GET', '/api/v1/account/pile/' . $pile->uuid . '/prompt');
        $promptInputs = [];
        $prompts = json_decode($promptsResponse);
        foreach ($prompts as $prompt) {
            $promptInputs[$prompt->uuid] = $this->ask($prompt->label);
        }
        return $promptInputs;
    }

    private function getFiles($pile, $promptInputs) {
        $apiClient = new \FilePile\FilePileLaravel\API\Client();
        $filesResponse = $apiClient->call('GET', '/api/v1/account/pile/' . $pile->uuid . '/file', [
            'prompts' => $promptInputs,
        ]);
        return json_decode($filesResponse);
    }

    private function getWriteFilesConfirmation($pileBaseInstallPath, $files) {
        if (!config('filepile.confirmInstallation')) {
            return true;
        }
        $this->info('FilePile is about to write the following files:');
        foreach ($files as $file) {
            $this->info('*' . $this->fullFilePath($pileBaseInstallPath, $file));
        }
        $confirmationInput = $this->ask(
            'Continue?',
            'Y'
        );
        if (strtoupper($confirmationInput) == 'Y') {
            return true;
        }
        return false;
    }

    private function fullFilePath($pileBaseInstallPath, $file) {
        $pileBaseInstallPath = trim($pileBaseInstallPath, '/');
        $filePath = trim($file->path, '/');
        return $pileBaseInstallPath . '/' . $filePath;
    }

    private function installFiles($pileBaseInstallPath, $files) {
        foreach ($files as $file) {
            $this->installFile($pileBaseInstallPath, $file);
        }
    }

    private function installFile($pileBaseInstallPath, $file) {
        $fullFilePath = $this->fullFilePath($pileBaseInstallPath, $file);
        if (file_exists(base_path($fullFilePath))) {
            $this->error('-File exists: ' . $fullFilePath);
        } else {
            $this->info('+ Creating: ' . $fullFilePath);
            $fileWriter = new FileWriter($fullFilePath);
            try {
                if ($fileWriter->createDirectories() && $fileWriter->createFile()) {
                    $fileWriter->addContent(base64_decode($file->content));
                }
            }catch (FileException $exception){
                $this->error('Error: '.$exception->getMessage());
            }
        }
    }

    private function showPostInstallationMessage($pile) {
        if (!empty($pile->post_install_message)) {
            $this->info("\n" . $pile->post_install_message);
        }
    }

}