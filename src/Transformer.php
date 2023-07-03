<?php

namespace Axn\RevertDbDefaultStringLength;

use Doctrine\DBAL\Schema\AbstractSchemaManager as DoctrineSchemaManager;
use Doctrine\DBAL\Types\StringType;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder as SchemaBuilder;

class Transformer
{
    protected Connection $connection;

    protected DoctrineSchemaManager $doctrineSchemaManager;

    protected SchemaBuilder $schemaBuilder;

    protected ?Command $command = null;

    protected array $stringColumnsInfo = [];

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->doctrineSchemaManager = $connection->getDoctrineSchemaManager();
        $this->schemaBuilder = $connection->getSchemaBuilder();

        // Prevention of errors in the presence of enum type columns
        $this->connection->getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Set console command instance for printing message to the console.
     *
     * @param  Command $command
     * @return void
     */
    public function setConsoleCommand(Command $command)
    {
        $this->command = $command;
    }

    public function transform()
    {
        $this->extractSchemaInfos();

        foreach ($this->stringColumnsInfo as $column) {
            $this->message("Change string length to 255 for {$column['table']}.{$column['column']}");

            $this->schemaBuilder->table($column['table'], function (Blueprint $blueprint) use ($column) {
                $blueprint
                    ->string($column['column'], 255)
                    ->nullable($column['nullable'])
                    ->default($column['default'])
                    ->change();
            });
        }
    }

    protected function extractSchemaInfos()
    {
        $this->stringColumnsInfo = [];

        foreach ($this->doctrineSchemaManager->listTables() as $table) {
            foreach ($table->getColumns() as $column) {
                if (! $column->getType() instanceof StringType || $column->getLength() != 191) {
                    continue;
                }

                $this->stringColumnsInfo[] = [
                    'table' => $table->getName(),
                    'column' => $column->getName(),
                    'nullable' => ! $column->getNotnull(),
                    'default' => $column->getDefault(),
                ];
            }
        }
    }

    /**
     * Print message to the console if set, or do an echo.
     *
     * @param  string $message
     * @param  string $style
     * @return void
     */
    protected function message($message, $style = 'info')
    {
        if ($this->command !== null) {
            $this->command->line($message, $style);
        } else {
            echo $message."\n";
        }
    }
}
