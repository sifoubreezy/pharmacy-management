<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Conversation extends Model
{
    public function messages(): Relation{
        return $this->hasMany(Messages::class);
    }
}
