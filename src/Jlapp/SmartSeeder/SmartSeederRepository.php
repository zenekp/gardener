<?php

namespace Jlapp\SmartSeeder;

use App;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;

class SmartSeederRepository implements MigrationRepositoryInterface
{
    /**
     * The database connection resolver instance.
     *
     * @var Resolver
     */
    protected $resolver;

    /**
     * The name of the migration table.
     *
     * @var string
     */
    protected $table;

    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection;

    /**
     * The name of the environment to run in.
     *
     * @var string
     */
    public $env;

    /**
     * Create a new database migration repository instance.
     *
     * @param Resolver $resolver
     * @param string                                           $table
     */
    public function __construct(Resolver $resolver, $table)
    {
        $this->table = $table;
        $this->resolver = $resolver;
    }

    /**
     * Set the environment to run the seeds against.
     *
     * @param $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * Get the ran migrations.
     *
     * @return array
     */
    public function getRan()
    {
        $env = $this->env;

        if (empty($env)) {
            $env = App::environment();
        }

        return $this->table()
            ->where('env', '=', $env)
            ->pluck('seed')
            ->all();
    }

    /**
     * Get the list of migrations.
     *
     * @param  int  $steps
     * @return array
     */
    public function getMigrations($steps)
    {
        return [];
    }

    /**
     * Get the list of the migrations by batch.
     *
     * @param  int  $batch
     * @return array
     */
    public function getMigrationsByBatch($batch)
    {
        return [];
    }

    /**
     * Get the last migration batch.
     *
     * @return array
     */
    public function getLast()
    {
        $env = $this->env;

        if (empty($env)) {
            $env = App::environment();
        }

        return $this->table()
            ->where('env', '=', $env)
            ->where('batch', $this->getLastBatchNumber())
            ->orderBy('seed', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get the completed migrations with their batch numbers.
     *
     * @return array
     */
    public function getMigrationBatches()
    {
        return [];
    }

    /**
     * Log that a migration was run.
     *
     * @param  string $file
     * @param  int    $batch
     * @return void
     */
    public function log($file, $batch)
    {
        $env = $this->env;

        if (empty($env)) {
            $env = App::environment();
        }

        $this->table()->insert([
            'seed' => $file,
            'env' => $env,
            'batch' => $batch,
        ]);
    }

    /**
     * Remove a migration from the log.
     *
     * @param  object  $migration
     * @return void
     */
    public function delete($migration)
    {
        $env = $this->env;

        if (empty($env)) {
            $env = App::environment();
        }

        $this->table()
            ->where('env', '=', $env)
            ->where('seed', $migration->seed)
            ->delete();
    }

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber()
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public function createRepository()
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        $schema->create($this->table, function (Blueprint $table) {
            // The migrations table is responsible for keeping track of which of the
            // migrations have actually run for the application. We'll create the
            // table to hold the migration file's path as well as the batch ID.
            $table->string('seed');
            $table->string('env');
            $table->integer('batch');
        });
    }

    /**
     * Determine if the migration repository exists.
     *
     * @return bool
     */
    public function repositoryExists()
    {
        $schema = $this->getConnection()->getSchemaBuilder();
        return $schema->hasTable($this->table);
    }

    /**
     * Delete the migration repository data store.
     *
     * @return void
     */
    public function deleteRepository()
    {
        return;
    }

    /**
     * Set the information source to gather data.
     *
     * @param  string $name
     * @return void
     */
    public function setSource($name)
    {
        $this->connection = $name;
    }

    /**
     * Get the last migration batch number.
     *
     * @return int
     */
    public function getLastBatchNumber()
    {
        $env = $this->env;

        if (empty($env)) {
            $env = App::environment();
        }

        return $this->table()->where('env', '=', $env)->max('batch');
    }

    /**
     * Get a query builder for the migration table.
     *
     * @return Builder
     */
    protected function table()
    {
        return $this->getConnection()->table($this->table);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return Resolver
     */
    public function getConnectionResolver()
    {
        return $this->resolver;
    }

    /**
     * Resolve the database connection instance.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->resolver->connection($this->connection);
    }
}
