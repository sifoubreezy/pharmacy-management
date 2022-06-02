<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|null provider_id
 * @property mixed|null remise
 * @property mixed|null total_net
 * @property mixed|null total_h_t
 * @property mixed|null num_invoice
 */
class InvoicesRef extends Model
{
    //
    /**
     * @var mixed|null
     */
    private $created_date;
}
