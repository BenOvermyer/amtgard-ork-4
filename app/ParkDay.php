<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParkDay extends Model
{
    protected $table = 'ork_parkday';
    protected $primaryKey = 'parkday_id';

    public function park()
    {
        return $this->belongsTo( 'App\Park' );
    }
}
