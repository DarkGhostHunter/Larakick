<?php

namespace DarkGhostHunter\Larakick\Parsing\Database\Pipes;

use Closure;
use LogicException;
use Illuminate\Support\Str;
use DarkGhostHunter\Larakick\Scaffold;
use DarkGhostHunter\Larakick\Lexing\Database\Column;
use DarkGhostHunter\Larakick\Lexing\Database\Migration;

class ParseMigrations
{
    /**
     * Handle the parsing of the Database scaffold.
     *
     * @param  \DarkGhostHunter\Larakick\Scaffold  $scaffold
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Scaffold $scaffold, Closure $next)
    {
        foreach ($scaffold->database->models as $key => $model) {
            // We will abort if the developer already has a migration with the same name for a model.
            if ($scaffold->rawDatabase->has("migrations.{$model->table}")) {
                throw new LogicException(
                    "The migration already has a table named {$model->table} for [$key]."
                );
            }
        }

        foreach ($scaffold->rawDatabase->get('migrations') as $table => $columns) {
            $scaffold->database->migrations->put($table, $this->createMigration($table, $columns));
        }

        return $next($scaffold);
    }

    /**
     * Creates a Migration.
     *
     * @param  string  $table
     * @param  array  $columns
     * @return \DarkGhostHunter\Larakick\Lexing\Database\Migration
     */
    protected function createMigration(string $table, array $columns)
    {
        $migration = new Migration([
            'table' => $table,
        ]);

        foreach ($columns as $name => $column) {
            $migration->columns->push(Column::createFromLine($name, $column));
        }

        return $migration;
    }
}
