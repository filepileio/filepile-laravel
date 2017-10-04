<?php

namespace FilePile\FilePileLaravel\Commands;

use FilePile\FilePileLaravel\Support\Version\ApplicationVersionChecker;
use Illuminate\Console\Command;

class FilePile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filepile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'An alias for filepile:list';

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
        $this->info('
  ___ _ _     ___ _ _     
 | __(_) |___| _ (_) |___ 
 | _|| | / -_)  _/ | / -_)
 |_| |_|_\___|_| |_|_\___|
                          
        ');
        $version = ApplicationVersionChecker::get();
        $this->info('Version: '.$version);
        $this->call('filepile:list', []);
    }
}