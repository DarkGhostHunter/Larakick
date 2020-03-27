<?php

namespace DarkGhostHunter\Larakick\Construction\Migration\Pipes;

use Closure;
use Nette\PhpGenerator\ClassType;
use Illuminate\Database\Migrations\Migration;
use DarkGhostHunter\Larakick\Lexing\Code\Argument;
use DarkGhostHunter\Larakick\Lexing\Database\Migration as MigrationLexing;
use DarkGhostHunter\Larakick\Construction\Migration\MigrationConstruction;

class SetUpBlueprint
{
    /**
     * Handle the migration construction.
     *
     * @param  \DarkGhostHunter\Larakick\Construction\Migration\MigrationConstruction  $construction
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(MigrationConstruction $construction, Closure $next)
    {
        $construction->class->addMethod('up')
            ->addComment('Run the migrations.')
            ->addComment("\n")
            ->addComment('@return void')
            ->addBody($this->manageUpMethod($construction->migration));

        return $next($construction);
    }

    /**
     * Add the code schema for creating a new table.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Migration  $migration
     * @return string
     */
    protected function manageUpMethod(MigrationLexing $migration)
    {
        $start = '        Schema::create(\'' . $migration->table . '\', function (Blueprint $table) {';

        return $start . implode("\n", [
            $this->createColumns($migration),
            $this->createPrimary($migration),
            $this->createIndexes($migration),
        ]) . "\n" . '    });';
    }

    /**
     * Creates columns for the table.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Migration  $migration
     * @return string
     */
    protected function createColumns(MigrationLexing $migration)
    {
        foreach ($migration->columns as $column) {
            $string = "            \$table->{$column->type}({$column->name})";

            foreach ($column->methods as $method) {
                $string .= "->{$method->name}(";

                $string .=  $method->arguments->transform(function (Argument $argument) {
                    return '\'' . $argument->string . '\'';
                })->implode(',');

                $string .= ')';
            }

            $string .= ';)' . "\n";
        }

        return $string ?? '';
    }

    /**
     * Creates a Primary key.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Migration  $migration
     * @return string
     */
    protected function createPrimary(MigrationLexing $migration)
    {
        if ($migration->primary) {
            $string = "            \$table->primary('{$migration->primary}');\n";
        }

        return $string ?? '';
    }

    /**
     * Create the indexes.
     *
     * @param  \DarkGhostHunter\Larakick\Lexing\Database\Migration  $migration
     * @return string
     */
    protected function createIndexes(MigrationLexing $migration)
    {
        $string = '';

        foreach ($migration->indexes as $name => $index) {
            if (is_int($name)) {
                $string .= "            \$table->index('{$index}');\n";
            } else {
                $string .= "            \$table->index('{$index}', '{$name}');\n";
            }
        }

        return $string;
    }

}
