<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'ork_event';
    protected $primaryKey = 'event_id';

    public function kingdom()
    {
        return $this->belongsTo( 'App\Kingdom' );
    }

    public function park()
    {
        return $this->belongsTo( 'App\Park' );
    }

    public function details()
    {
        return $this->hasMany( 'App\EventCalendarDetails' );
    }
}
