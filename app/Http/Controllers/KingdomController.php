<?php namespace App\Http\Controllers;

use App\EventCalendarDetails;
use Cache;
use DB;
use App\Kingdom;

class KingdomController extends Controller
{
    public function show( $id )
    {
        $kingdom = Cache::remember( 'kingdom.' . $id . '.show', config( 'cache.kingdoms' ), function () use ( $id ) {
            return Kingdom::findOrFail( $id );
        } );

        $events = Cache::remember( 'kingdom.' . $id . '.show.events', config( 'cache.kingdoms' ), function () use ( $id ) {
            $sql = <<<SQL
SELECT e.name name, d.event_start, p.name park, d.event_calendardetail_id detail_id
FROM ork_event e
INNER JOIN ork_event_calendardetail d ON e.event_id = d.event_calendardetail_id
INNER JOIN ork_park p ON e.park_id = p.park_id
INNER JOIN ork_kingdom k ON e.kingdom_id = k.kingdom_id
WHERE d.event_start >= NOW()
AND k.kingdom_id = ?
ORDER BY d.event_start ASC
SQL;
            return DB::select( $sql, [ $id ] );
        });

        return view( 'kingdom.show' )->with( [ 'kingdom' => $kingdom, 'events' => $events, 'pageTitle' => $kingdom->name ] );
    }
}