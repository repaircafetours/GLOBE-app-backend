<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\BelongsToMany;
use WendellAdriel\Lift\Lift;

#[BelongsToMany(Volunteer::class, pivotModel: "volunteer_roles")]
class Role extends Model
{
    use Lift;

    public $timestamps = false;

    public string $name;
}
