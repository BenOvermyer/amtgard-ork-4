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
SELECT
DISTINCT
e.*,
k.name AS kingdom_name,
p.name AS park_name,
m.persona,
cd.event_start,
cd.event_calendardetail_id,
u.name AS unit_name,
SUBSTRING(cd.description, 1, 100) AS short_description
FROM ork_event e
LEFT JOIN ork_kingdom k ON k.kingdom_id = e.kingdom_id
LEFT JOIN ork_park p ON p.park_id = e.park_id
LEFT JOIN ork_mundane m ON m.mundane_id = e.mundane_id
LEFT JOIN ork_event_calendardetail cd ON e.event_id = cd.event_id
LEFT JOIN ork_unit u ON e.unit_id = u.unit_id
WHERE
e.kingdom_id = ?
AND e.park_id = 0
AND cd.event_start IS NOT NULL
AND cd.event_start > DATE_ADD(NOW(), INTERVAL - 7 DAY)
AND cd.current = 1
ORDER BY
cd.event_start,
kingdom_name,
park_name,
e.name
SQL;
            return DB::select( $sql, [ $id ] );
        } );

        $officers = Cache::remember( 'kingdom.' . $id . '.show.officers', config( 'cache.kingdoms' ), function () use ( $id ) {
            $sql = <<<SQL
SELECT
a.*,
p.name AS park_name,
k.name AS kingdom_name,
e.name AS event_name,
u.name AS unit_name,
m.username,
m.given_name,
m.surname,
m.persona,
m.restricted,
m.mundane_id,
o.role AS officer_role,
o.officer_id
FROM ork_officer o
LEFT JOIN ork_mundane m ON o.mundane_id = m.mundane_id
LEFT JOIN ork_authorization a ON a.authorization_id = o.authorization_id
LEFT JOIN ork_park p ON a.park_id = p.park_id
LEFT JOIN ork_kingdom k ON a.kingdom_id = k.kingdom_id
LEFT JOIN ork_event e ON a.event_id = e.event_id
LEFT JOIN ork_unit u ON a.unit_id = u.unit_id
WHERE o.kingdom_id = ?
AND o.park_id = 0
SQL;
            return DB::select( $sql, [ $id ] );
        } );

        return view( 'kingdom.show' )->with( [ 'kingdom' => $kingdom, 'events' => $events, 'officers' => $officers, 'pageTitle' => $kingdom->name ] );
    }
}