<?php

namespace Kerrinhardy\Ignite;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;

class IgniteMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ignite:migration {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations including Model, Migration, Controller and Request.';

    /**
     * Get the relevant stub file for the command.
     */
    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . '/resources/stubs/' . $type . '.stub');
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return base_path() . '/database/migrations/' . $this->getDatePrefix() . '_create_' . Str::plural(strtolower($name)) . '_table.php';
    }

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
     * Create a new Migration from the stub and the names entered in the command.
     *
     * @var string
     * @var array
     */
    protected function migration($name, $columns, $other_migrations)
    {
        $migrations = '';

        $parameters_not_required = [
            'bigIncrements',
            'bigInteger',
            'binary',
            'boolean',
            'date',
            'dateTime',
            'dateTimeTz',
            'geometry',
            'geometryCollection',
            'increments',
            'integer',
            'ipAddress',
            'json',
            'jsonb',
            'lineString',
            'longText',
            'macAddress',
            'mediumIncrements',
            'mediumInteger',
            'mediumText',
            'morphs',
            'uuidMorphs',
            'multiLineString',
            'multiPoint',
            'multiPolygon',
            'nullableMorphs',
            'nullableUuidMorphs',
            'point',
            'polygon',
            'smallIncrements',
            'smallInteger',
            'string',
            'text',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
            'tinyIncrements',
            'tinyInteger',
            'unsignedBigInteger',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger',
            'uuid',
            'year'
        ];

        $parameters_digits = [
            'decimal',
            'double',
            'float',
            'unsignedDecimal'
        ];

        foreach ($columns as $column => $type) {
            if (in_array($type, $parameters_not_required)) {
                $migrations .= '$table->' . $type . '(\'' . $column . '\');' . PHP_EOL;
            }
            if (strpos($type, "char") === 0) {
                $input = explode("-", $type);
                $migrations .= '$table->' . $input[0] . '(\'' . $column . '\', ' . $input[1] . ');' . PHP_EOL;
            }
            if (strpos($type, "string") === 0) {
                $input = explode("-", $type);
                $migrations .= '$table->' . $input[0] . '(\'' . $column . '\', ' . $input[1] . ');' . PHP_EOL;
            }
            if (in_array($type, $parameters_digits) === 0) {
                $input = explode("-", $type);
                $migrations .= '$table->' . $input[0] . '(\'' . $column . '\', ' . $input[1] . ', ' . $input[2] . ');' . PHP_EOL;
            }
            if (strpos($type, "enum") === 0) {
                $input = explode("-", $type);
                $migrations .= '$table->' . $input[0] . '(\'' . $column . '\', [\'' . $input[1] . '\']);' . PHP_EOL;
            }
            if (strpos($type, "set") === 0) {
                $input = explode("-", $type);
                $migrations .= '$table->' . $input[0] . '(\'' . $column . '\', [\'' . $input[1] . '\']);' . PHP_EOL;
            }
        }

        foreach ($other_migrations as $key => $value) {
            $migrations .= '$table->' . $value . '();' . PHP_EOL;
        }

        $migrationTemplate = str_replace(
            [
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{columns}}'
            ],
            [
                Str::plural(Str::studly($name)),
                Str::plural(strtolower($name)),
                $migrations
            ],
            $this->getStub('Migration')
        );

        file_put_contents($this->getPath($name), $migrationTemplate);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $col_name = [];
        $col_type = [];

        $i = 0;

        $options = [
            'bigIncrements',
            'bigInteger',
            'binary',
            'boolean',
            'char',
            'date',
            'dateTime',
            'dateTimeTz',
            'decimal',
            'double',
            'enum',
            'float',
            'geometry',
            'geometryCollection',
            'increments',
            'integer',
            'ipAddress',
            'json',
            'jsonb',
            'lineString',
            'longText',
            'macAddress',
            'mediumIncrements',
            'mediumInteger',
            'mediumText',
            'morphs',
            'uuidMorphs',
            'multiLineString',
            'multiPoint',
            'multiPolygon',
            'nullableMorphs',
            'nullableUuidMorphs',
            'point',
            'polygon',
            'set',
            'smallIncrements',
            'smallInteger',
            'string',
            'text',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
            'tinyIncrements',
            'tinyInteger',
            'unsignedBigInteger',
            'unsignedDecimal',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger',
            'uuid',
            'year'
        ];

        $rows = array_chunk($options, 6);
        $headers = ['Column Type', '', '', '', '', ''];

        do {
            $i++;
            $col_name[$i] = $this->ask('Column name?');
            if ($col_name[$i] != null) {
                $this->table($headers, $rows);
                $col_type[$i] = $this->anticipate('Column Type?', $options);
                if ($col_type[$i] == "char" or $col_type[$i] == "string") {
                    $col_type[$i] .= '-' . $this->anticipate('Length?', [100, 255]);
                }
                if ($col_type[$i] == "decimal" or $col_type[$i] == "double" or $col_type[$i] == "float" or $col_type[$i] == "unsignedDecimal") {
                    $precision = $this->anticipate('Total digits (precision)?', [8]);
                    $scale = $this->anticipate('Decimal digits (scale)?', [2]);
                    $col_type[$i] .= '-' . $precision . '-' . $scale;
                }
                if ($col_type[$i] == "enum" or $col_type[$i] == "set") {
                    $j = 0;
                    $elements = [];
                    do {
                        $j++;
                        $elements[$j] = $this->ask('Element name?');
                    } while ($elements[$j] != null);
                    array_pop($elements);
                    $col_type[$i] .= '-' . implode('\', \'', $elements);
                }
            }
        } while ($col_name[$i] != null);

        array_pop($col_name);

        $columns = array_combine($col_name, $col_type);

        $this->info('Main columns for ' . $name . ' table created successfully.');

        $other = [];
        $other_migrations = [];

        $k = 0;

        do {
            $k++;
            $other[$k] = $this->confirm('Other migrations?');
            if ($other[$k] != false) {
                $other_migrations[$k] = $this->choice('Select migration type?', [
                    "0" => 'nullableTimestamps',
                    "1" => 'rememberToken',
                    "2" => 'softDeletes',
                    "3" => 'softDeletesTz',
                    "4" => 'timestamps',
                    "5" => 'timestampsTz',
                ], 'timestamps');
            }
        } while ($other[$k] != false);

        $this->migration($name, $columns, $other_migrations);

        $this->info('Migration for ' . $name . ' created successfully.');

    }
}
