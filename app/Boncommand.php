<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Fournisseur;
class Boncommand extends Model
{
    protected $table = 'boncommands';
    protected $primaryKey = 'id';

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id', 'id');
    }
}
