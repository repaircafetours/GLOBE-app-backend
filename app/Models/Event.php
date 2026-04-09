<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use WendellAdriel\Lift\Attributes\Cast;
use WendellAdriel\Lift\Attributes\Relations\BelongsToMany;
use WendellAdriel\Lift\Attributes\Relations\HasManyThrough;
use WendellAdriel\Lift\Lift;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes as SchemalessAttributesCast;
use Spatie\SchemalessAttributes\SchemalessAttributes;

#[BelongsToMany(Item::class, name: "appointments", pivotModel:Appointment::class)]
#[HasManyThrough(Visitor::class, Item::class)]
/**
 * @property Visitor[] $visitors
 */
class Event extends Model
{
    use HasSchemalessAttributes, Lift;

    public $timestamps = false;

    #[Cast("datetime")]
    public Carbon $date;
    public string $city;
    public string $zip_code;
    public string $address;

    #[Cast(SchemalessAttributesCast::class)]
    public SchemalessAttributes $extra_attributes;

    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            "appointment",
            "event_id",
            "item_id",
        )->using(Appointment::class)
         ->withPivot('comment', 'appointment_date', 'satisfaction_rating');
    }
}
