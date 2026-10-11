<?php

namespace TheRiptide\LaravelDynamicDashboard\Commands;

use App\Support\DripEmailer;
use Illuminate\Console\Command;
use TheRiptide\LaravelDynamicDashboard\Tools\CreateType;

class CreateTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dyndash:create {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a DynDash type';

    /**
     * Execute the console command.
     *
     * @param  DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        (new CreateType)->run($this->argument('type'));

        $this->info("Created DynDash type {$this->argument('type')}!");
    }
}
