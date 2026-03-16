<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\BelongsToMany;
use WendellAdriel\Lift\Lift;

/**
 * @property Speciality[] $speciality
 * @property Role[] $role
 */
#[BelongsToMany(Speciality::class, pivotModel: "volunteer_speciality")]
#[BelongsToMany(Role::class, pivotModel: "volunteer_roles")]
class Volunteer extends Model
{
    use HasSchemalessAttributes, Lift;

    public $timestamps = false;
    public int $idHumHub;
}
