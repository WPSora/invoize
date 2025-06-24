<?php

namespace Invoize\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    // protected $connection = 'wordpress';
    protected $table = 'options';
    protected $primaryKey = 'option_id';
    public $timestamps = false;
    protected $fillable = [
        'option_name',
        'option_value',
    ];
}
