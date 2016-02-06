<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AwardDetails extends Model
{
    protected $table = 'ork_award';
    protected $primaryKey = 'award_id';

    public function awards()
    {
        return $this->hasMany( 'App\Award' );
    }
}
