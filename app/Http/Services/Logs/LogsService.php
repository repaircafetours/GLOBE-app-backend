<?php

namespace App\Http\Services\Logs;

use App\Models\Logs\Logs;
use App\Models\Visitor;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Environment\Console;

class LogsService
{
    public function create(?Volunteer $volunteer = null): Logs
    {
        $log = new Logs();
        $log->updated_at = now();
        $log->volunteer_id = $volunteer?->id;
        $log->save();

        return $log;
    }

    /**
     * Compare deux instances d'un modèle et retourne les colonnes modifiées.
     * Seules les colonnes présentes dans information_schema sont comparées.
     *
     * @return array<array{table_name: string, column_name: string, old_value: mixed, new_value: mixed}>
     */
    public function buildUpdatedColumns(Model $old, Model $new): array
    {
        $table = $new->getTable();
        $database = DB::getDatabaseName();

        // Récupère les colonnes existantes depuis information_schema
        $schemaColumns = DB::table("information_schema.COLUMNS")
            ->where("TABLE_SCHEMA", $database)
            ->where("TABLE_NAME", $table)
            ->pluck("COLUMN_NAME")
            ->toArray();

        $changed = [];

        foreach ($schemaColumns as $column) {
            $oldValue = $old->{$column} ?? null;
            $newValue = $new->{$column} ?? null;
            if ($oldValue !== $newValue) {
                $changed[] = [
                    "table_name" => $table,
                    "column_name" => $column,
                ];
            }
        }

        return $changed;
    }

    /**
     * Attache les colonnes modifiées à un log existant.
     *
     * @param array<array{table_name: string, column_name: string, old_value: mixed, new_value: mixed}> $columns
     */
    public function attachColumns(Logs $log, array $columns): void
    {
        foreach ($columns as $column) {
            DB::table("logs_columns")->insert([
                "logs_id" => $log->id,
                "table_name" => $column["table_name"],
                "column_name" => $column["column_name"],
            ]);
        }
    }
}
