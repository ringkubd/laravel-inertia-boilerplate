<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;

class Meilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:update:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MeiliSearch Filter Attribute Update';

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
        $model = $this->ask('Please give me an index id...');
        $fields = $this->ask('And your Filterable Fields coiches are?');

        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $client->index($model)->updateFilterableAttributes(explode(',', $fields));
        $this->info($fields);
        return Command::SUCCESS;
    }
}
