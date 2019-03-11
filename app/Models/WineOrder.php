<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class WineOrder
 * @codeCoverageIgnore
 */
class WineOrder extends Model
{
    use SoftDeletes;

    protected $table      = 'wine_orders';
    protected $primaryKey = ['order_id', 'wine_guid'];
    protected $fillable   = [
        'status',
        'order_id',
        'wine_guid',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
