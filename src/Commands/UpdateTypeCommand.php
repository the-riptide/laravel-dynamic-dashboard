<?php

namespace TheRiptide\LaravelDynamicDashboard\Commands;
 
use Illuminate\Console\Command;
use TheRiptide\LaravelDynamicDashboard\Tools\UpdateType;
 
class UpdateTypeCommand extends Command
{
        /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dyndash:update';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update type to include new functionality';
 
    /**
     * Execute the console command.
     *
     * @param  \App\Support\DripEmailer  $drip
     * @return mixed
     */
    public function handle()
    {
        (new UpdateType)->run();

        $this->info("Updated DynDash types!");
    }
}