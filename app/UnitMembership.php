<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitMembership extends Model
{
    protected $table = 'ork_unit_mundane';
    protected $primaryKey = 'unit_mundane_id';

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function member()
    {
        return $this->belongsTo('App\Mundane', 'mundane_id');
    }
}
