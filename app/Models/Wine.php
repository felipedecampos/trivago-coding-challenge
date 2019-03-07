<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
