<?php

namespace TheRiptide\LaravelDynamicDashboard\Commands;
 
use Illuminate\Console\Command;
use TheRiptide\LaravelDynamicDashboard\Tools\ModifyType;
 
class ModifyTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dyndash:modify {type?}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'modify existing models to new fields';
 
    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        (new ModifyType)->run($this->argument('type'));

        $this->info("Modified DynDash type {$this->argument('type')}!");
    }
}