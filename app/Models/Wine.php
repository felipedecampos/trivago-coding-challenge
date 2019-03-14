<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Wine
 * @codeCoverageIgnore
 */
class Wine extends Model
{
    use SoftDeletes;

    protected $table      = 'wines';
    protected $primaryKey = 'guid';
    protected $hidden     = ['guid'];
    protected $fillable   = [
        'variety',
        'region',
        'year',
        'price',
        'link',
        'pub_date',
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

    /**
     * Retrieve wines of this order
     * @return BelongsToMany
     */
    public function order()
    {
        return $this->belongsToMany(Order::class, 'wine_orders', 'wine_guid', 'order_id')
            ->withPivot('status');
    }
}
