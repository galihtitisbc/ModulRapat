<?php

namespace Modules\Rapat\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrationRapat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rapat:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for migrate:fresh migration in core and Rapat';

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
        $this->call('migrate:fresh', [
            '--path' => 'database/migrations',
        ]);
        $this->call('module:migrate-refresh', [
            'module' => 'Rapat'
        ]);
        $this->info('Migration for core And Rapat executed successfully.');
        $this->call('db:seed', ['--class' => 'DatabaseSeeder']);
        $this->call('module:seed', ['module' => 'Rapat']);

        $this->info('Seeder for core And Rapat executed successfully.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
