<?php

namespace FilePile\FilePileLaravel\Commands;

use Illuminate\Console\Command;

class FilePileList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filepile:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available piles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $apiClient = new \FilePile\FilePileLaravel\API\Client();
        $response = $apiClient->call('GET','/api/v1/account/pile');
        $piles = json_decode($response);
        if(count($piles) > 0){
            $this->info('The following commands are available:');
            foreach($piles as $pile){
                $this->info('* filepile:install '.$pile->slug);
            }
        }else{
            $this->error('No commands available');
        }
    }
}