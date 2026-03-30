<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes as SchemalessAttributesCast;
use Spatie\SchemalessAttributes\SchemalessAttributes;
use WendellAdriel\Lift\Attributes\Cast;
use WendellAdriel\Lift\Attributes\Relations\BelongsToMany;
use WendellAdriel\Lift\Lift;

#[BelongsTo(Visitor::class)]
#[BelongsToMany(Event::class, pivotModel: Appointment::class)]
/**
 * @property Visitor $visitor
 */
class Item extends Model
{

use HasSchemalessAttributes, Lift;

    public $timestamps = false;

    #[Cast("float")]
    public float $weight;
    public int $age;
    public string $name;

    #[Cast("bool")]
    public bool $is_electric;
    public string $brand;

    //#[Cast(SchemalessAttributesCast::class)]
    //public SchemalessAttributes $extra_attributes;

}
