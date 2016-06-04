<?php

namespace App\Http\Controllers;

use App\Park;
use Cache;
use DB;

class ParkController extends Controller
{
    public function show( $id )
    {
        $park = Cache::remember( 'park.' . $id . '.show', config( 'cache.expiration' ), function () use ( $id ) {
            return Park::findOrFail( $id );
        } );

        $events = Cache::remember( 'park.' . $id . '.show.events', config( 'cache.expiration' ), function () use ( $id ) {
            $sql = <<<'SQL'
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
e.park_id = ?
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

        $officers = Cache::remember( 'park.' . $id . '.show.officers', config( 'cache.expiration' ), function () use ( $id ) {
            $sql = <<<'SQL'
SELECT
m.mundane_id,
m.persona,
o.role
FROM
ork_officer o
INNER JOIN ork_mundane m ON o.mundane_id = m.mundane_id
WHERE
o.park_id = ?
SQL;
            return DB::select( $sql, [ $id ] );
        } );

        return view( 'park.show' )->with( [
            'park' => $park,
            'events' => $events,
            'officers' => $officers,
            'pageTitle' => $park->name
        ] );
    }
}
