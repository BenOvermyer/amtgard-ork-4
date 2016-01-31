<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kingdom extends Model
{
    protected $primaryKey = 'kingdom_id';
    protected $table = 'ork_kingdom';

    public $timestamps = false;

    public function parks()
    {
        return $this->hasMany('App\Park');
    }

    public function activeParks()
    {
        return $this->hasMany('App\Park')->where('active', 'Active');
    }
}
