<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Relations\BelongsToMany;

/**
 * @property Speciality[] $speciality
 * @property Role[] $role
 */
#[BelongsToMany(Speciality::class, pivotModel: "volunteer_speciality")]
#[BelongsToMany(Role::class, pivotModel: "volunteer_roles")]
class Volunteer extends Model
{
    public int $idHumHub;
    public string $regime;

}
