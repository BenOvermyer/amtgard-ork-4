<?php namespace App\Http\Controllers;

use Cache;
use DB;
use App\Park;

class ParkController extends Controller
{
    public function show( $id )
    {
        $park = Cache::remember( 'park.' . $id . '.show', config( 'cache.expiration' ), function () use ( $id ) {
            return Park::findOrFail( $id );
        } );

        $events = Cache::remember( 'park.' . $id . '.show.events', config( 'cache.expiration' ), function () use ( $id ) {
            $sql = <<<SQL
SELECT e.name name, d.event_start, p.name park, d.event_calendardetail_id detail_id
FROM ork_event e
INNER JOIN ork_event_calendardetail d ON e.event_id = d.event_calendardetail_id
INNER JOIN ork_park p ON e.park_id = p.park_id
WHERE d.event_start >= NOW()
AND p.park_id = ?
ORDER BY d.event_start ASC
SQL;
            return DB::select( $sql, [ $id ] );
        });

        return view( 'park.show' )->with( [ 'park' => $park, 'events' => $events, 'pageTitle' => $park->name ] );
    }
}