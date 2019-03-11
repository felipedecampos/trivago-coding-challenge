<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Order
 * @codeCoverageIgnore
 */
class Order extends Model
{
    use SoftDeletes;

    protected $table      = 'orders';
    protected $primaryKey = 'id';
    protected $hidden     = ['id'];
    protected $fillable   = [
        'status',
        'user_id',
        'waiter_id',
        'sommelier_id',
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

    public static function boot()
    {
        parent::boot();

        static::deleted(function(Order $order)
        {
            $order->wine_order()->delete();
        });
    }

    /**
     * Retrieve waiter of this order
     * @return HasOne
     */
    public function waiter()
    {
        return $this->hasOne(Waiter::class, 'id', 'waiter_id');
    }

    /**
     * Retrieve wines of this order
     * @return BelongsToMany
     */
    public function wine_order()
    {
        return $this->belongsToMany(Wine::class, 'wine_orders', 'order_id', 'wine_guid')
            ->withPivot('status');
    }
}
