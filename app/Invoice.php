<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed ref_invoice_id
 */
class Invoice extends Model
{
    
    //
    /**
     * @var mixed|null
     */
    private $created_date;
    /**
     * @var mixed|null
     */
    private $provider_id;
    /**
     * @var mixed|null
     */
    private $remise;
    /**
     * @var date|null
     */
    private $total_net;
    /**
     * @var mixed|null
     */
    private $total_h_t;
    /**
     * @var mixed
     */
    private $com_name;
    /**
     * @var mixed
     */
    private $quantity;
    /**
     * @var mixed
     */
    private $date_perm;
    /**
     * @var mixed
     */
    private $pv_ht;
    /**
     * @var mixed
     */
    private $ppa;
    /**
     * @var mixed
     */
    private $tag;
    /**
     * @var mixed
     */
    private $CTherapeutique;
    /**
     * @var mixed
     */
    private $Conditionnement;
    /**
     * @var mixed
     */
    private $cart_id;
    /**
     * @var mixed
     */
    private $offre;
    /**
     * @var mixed
     */
    private $image;
    /**
     * @var mixed
     */
    private $post_id;
}
