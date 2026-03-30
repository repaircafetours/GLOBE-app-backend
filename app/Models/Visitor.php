<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use WendellAdriel\Lift\Lift;

use App\Traits\HasSchemalessAttributes;
use WendellAdriel\Lift\Attributes\Cast;
use WendellAdriel\Lift\Attributes\Relations\HasManyThrough;
use WendellAdriel\Lift\Attributes\Rules;

use Spatie\SchemalessAttributes\Casts\SchemalessAttributes as SchemalessAttributesCast;
use Spatie\SchemalessAttributes\SchemalessAttributes;

/**
 * @property Item[] $items
 * @property Visitor[] $visitors
 */
#[HasMany(Item::class)]
#[HasManyThrough(Event::class, Item::class)]
class Visitor extends Model
{
    use HasSchemalessAttributes, Lift;
    public $timestamps = false;

    public string $email;

    public string $title;

    #[Rules(["required"])]
    public string $name;
    public string $surname;
    public string $zip_code;
    public string $city;
    public ?string $phone_number;
    public ?string $source;

    #[Cast("bool")]
    public bool $notification;

    #[Cast(SchemalessAttributesCast::class)]
    public SchemalessAttributes $extra_attributes;
}
