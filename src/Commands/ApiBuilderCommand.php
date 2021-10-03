<?php

namespace BgpGroup\ApiBuilder\Commands;

use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;

class ApiBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:api {endpoint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate api crud endpoints';

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
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        $endpoint = $this->argument('endpoint');

        $this->info('This is the endpoint: ' . $endpoint);
    }
}