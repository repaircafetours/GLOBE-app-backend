<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;
use WendellAdriel\Lift\Attributes\PrimaryKey;
use WendellAdriel\Lift\Attributes\Relations\HasOne;
use WendellAdriel\Lift\Attributes\Rules;
use WendellAdriel\Lift\Lift;

#[HasOne(Item::class)]
#[HasOne(Event::class)]
class Appointment extends Pivot
{
    use HasSchemalessAttributes;

    public ?string $comment;

    protected $fillable = [
        'comment',
        'appointment_date',
        'satisfaction_rating',
        'extra_attributes',
    ];

    public $timestamps = false;

    #[PrimaryKey(Carbon::class, false)]
    public Carbon $appointment_date;

    #[PrimaryKey(incrementing: false)]
    public int $item_id;

    #[PrimaryKey(incrementing: false)]
    public int $event_id;

    #[Rules(["nullable", "min:1 max:5"], ["min" => "The satisfaction rating must be at least 1.", "max" => "The satisfaction rating may not be greater than 5."])]
    // #[Rules(min:1, max:5, messages:["min" => "The satisfaction rating must be at least 1.", "max" => "The satisfaction rating may not be greater than 5."])]
    public int $satisfaction_rating;
}
