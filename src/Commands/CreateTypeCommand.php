<?php

namespace TheRiptide\LaravelDynamicDashboard\Commands;
 
use TheRiptide\LaravelDynamicDashboard\Tools\CreateType;
use Illuminate\Console\Command;
 
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
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        (new CreateType)->run($this->argument('type'));

        $this->info("Created DynDash type {$this->argument('type')}!");
    }
}