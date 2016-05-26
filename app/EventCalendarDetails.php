<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventCalendarDetails extends Model
{
    protected $table = 'ork_event_calendardetail';
    protected $primaryKey = 'event_calendardetail_id';

    public function event()
    {
        return $this->belongsTo('App\Event');
    }
}
