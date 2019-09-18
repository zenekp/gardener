<?php

namespace Jlapp\SmartSeeder;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;

class SeedCommand extends Command
{
    use ConfirmableTrait;

    /**
     * Migrator.
     *
     * @var object
     */
    private $migrator;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds the database';

    /**
     * Constructor.
     *
     * @param SeedMigrator $migrator
     */
    public function __construct(SeedMigrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $path = database_path(config('seeds.dir'));
        $env = $this->option('env');
        $single = $this->option('file');

        $this->prepareDatabase();
        $this->migrator->setEnv($env);

        // The pretend option can be used for "simulating" the migration and grabbing
        // the SQL queries that would fire if the migration were to be run against
        // a database for real, which is helpful for double checking migrations.
        $options = [
            'pretend' => $this->input->getOption('pretend'),
        ];

        if ($single) {
            $this->migrator->runSingleFile("$path/$single", $options);
        } else {
            $this->migrator->run($path, $options);
        }

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
    }

    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->input->getOption('database'));

        if (! $this->migrator->repositoryExists()) {
            $options = [
                '--database' => $this->input->getOption('database'),
            ];

            $this->call('seed:install', $options);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['env', null, InputOption::VALUE_OPTIONAL, 'The environment in which to run the seeds.', null],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['file', null, InputOption::VALUE_OPTIONAL, 'Allows individual seed files to be run.', null],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
