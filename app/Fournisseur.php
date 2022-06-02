<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Boncommand;
class Fournisseur extends Model
{
    protected $table = 'fournisseurs';
    protected $primaryKey = 'id';

    public function Boncommand()
    {
        return $this->belongsTo(Boncommand::class, 'fournisseur_id', 'id');
    }
}
