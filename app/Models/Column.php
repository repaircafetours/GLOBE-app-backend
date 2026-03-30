<?php

namespace App\Models;

use App\Models\Logs\Logs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[BelongsToMany(Logs::class, pivotModel: "logs_columns")]
class Column extends Model
{
    public string $name;
    public string $table_name;
}
