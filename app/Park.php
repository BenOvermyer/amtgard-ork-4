<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $table = 'ork_park';
    protected $primaryKey = 'park_id';

    public function kingdom()
    {
        return $this->belongsTo('App\Kingdom');
    }
}
