<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use WendellAdriel\Lift\Attributes\Relations\BelongsToMany;
use WendellAdriel\Lift\Lift;

/**
 * @property int         $id
 * @property int         $idHumHub
 * @property string|null $login
 * @property string|null $password
 * @property Role[]      $roles
 * @property Speciality[] $specialities
 */
#[BelongsToMany(Speciality::class, table: "volunteer_speciality")]
#[BelongsToMany(Role::class, table: "volunteer_roles")]
class Volunteer extends Authenticatable
{
    use HasApiTokens, HasSchemalessAttributes, Lift;

    public $timestamps = false;

    /**
     * login is intentionally NOT declared as a Lift public property.
     * Declaring it as `public ?string $login = null` causes Lift to shadow
     * the DB value with the PHP default. Standard Eloquent __get() is used
     * instead, and the column is made fillable via $fillable below.
     */
    protected $fillable = ["login"];

    protected $hidden = ["password"];

    protected $casts = [
        "password" => "hashed",
    ];

    public int $idHumHub;
}
