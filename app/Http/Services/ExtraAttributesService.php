<?php

namespace App\Http\Services;

use App\Traits\HasSchemalessAttributes;
use Illuminate\Database\Eloquent\Model;

class ExtraAttributesService {

    /**
     * Insert any updates or addition to the extra_attributes field on the model
     * @template T of HasSchemalessAttributes & Model
     * @param T $model Any model that posseses the trait HasSchemalessAttributes
     * @param array[mixed] $extra_attributes
     * @return void
     */
    public function updateAttributes($model, array $extra_attributes) {
        foreach ($extra_attributes as $key => $value) {
            $model->extra_attributes->set($key, $value);
        }
    }

}