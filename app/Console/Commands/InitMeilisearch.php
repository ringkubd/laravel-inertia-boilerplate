<?php

namespace App\Console\Commands;

use App\Models\MadrasahResult;
use App\Models\Student;
use Illuminate\Console\Command;

class InitMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =  'init:meilisearch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  'Inits Meilisearch indexes data';

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
     * @return int
     */
    public function handle()
    {
        MadrasahResult::makeAllSearchable();
        Student::makeAllSearchable();
    }
}
