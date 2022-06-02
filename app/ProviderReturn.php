<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderReturn extends Model
{
    public function returnContents()
    {
        return $this->hasMany(ProviderReturnContent::class, 'return_id', 'id');
    }
}
