<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $table = 'ork_awards';
    protected $primaryKey = 'awards_id';

    public function details()
    {
        if ($this->award_id == 0) {
            $column = 'kingdomaward_id';
        } else {
            $column = 'award_id';
        }

        return $this->belongsTo('App\AwardDetails', $column);
    }

    public function player()
    {
        return $this->hasOne('App\Mundane');
    }

    public function park()
    {
        return $this->hasOne('App\Park');
    }

    public function kingdom()
    {
        return $this->hasOne('App\Kingdom');
    }

    public function atEvent()
    {
        return $this->belongsTo('App\Event', 'at_event_id');
    }

    public function atPark()
    {
        return $this->belongsTo('App\Park', 'at_park_id');
    }

    public function atKingdom()
    {
        return $this->belongsTo('App\Kingdom', 'at_kingdom_id');
    }

    public function givenBy()
    {
        return $this->belongsTo('App\Mundane', 'given_by_id');
    }
}
