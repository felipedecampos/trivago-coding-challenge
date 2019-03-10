<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Sommelier
 * @codeCoverageIgnore
 */
class Sommelier extends Model
{
    use SoftDeletes;

    protected $table      = 'sommeliers';
    protected $primaryKey = 'id';
    protected $hidden     = ['id'];
    protected $fillable   = [
        'first_name',
        'last_name',
        'available',
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
