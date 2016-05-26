<?php

namespace App\Http\Controllers;

use App\Mundane;
use Cache;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $minutes = config('cache.expiration');

        $data = Cache::remember('kingdoms.summary.homepage', $minutes, function () {
            $sql = <<<'SQL'
SELECT
k.name,
k.kingdom_id,
k.has_heraldry,
k.parent_kingdom_id,
ifnull(pcount.park_count,0) park_count,
ifnull(attendance_count,0) attendance,
ifnull(monthly_attendance_count,0) monthly,
ifnull(activeparks.parkcount,0) active_parks
FROM `ork_kingdom` k
left join
    (
        select count(*) as park_count, pcnt.kingdom_id from `ork_park` pcnt where pcnt.active = 'Active' group by pcnt.kingdom_id
    ) pcount on pcount.kingdom_id = k.kingdom_id
left join
    (
        select count(mundanesbyweek.mundane_id) attendance_count, mundanesbyweek.kingdom_id
        from
            (
                select mundane_id, week(date,3) as week, kingdom_id
                from ork_attendance
                where date > adddate(curdate(), interval - 26 week) group by week(date,3), mundane_id
            ) mundanesbyweek group by kingdom_id
    ) total_attendance on total_attendance.kingdom_id = k.kingdom_id
left join
    (
        select count(mundanesbymonth.mundane_id) monthly_attendance_count, mundanesbymonth.kingdom_id
        from
            (
                select mundane_id, month(date) as month, kingdom_id
                from ork_attendance
                where date > adddate(curdate(), interval - 12 month) group by month(date), mundane_id
            ) mundanesbymonth group by kingdom_id
    ) monthly_attendance on monthly_attendance.kingdom_id = k.kingdom_id
left join
    (
        select count(*) parkcount, kingdom_id
        from
            (
                select mundanesbyweek.kingdom_id
                from
                    (
                        select kingdom_id, park_id
                        from ork_attendance
                        where date > adddate(curdate(), interval - 4 week) group by week(date,3), mundane_id
                    ) mundanesbyweek group by kingdom_id, park_id
            ) parkcount group by kingdom_id) activeparks on activeparks.kingdom_id = k.kingdom_id
where active = 'Active'
order by k.name
SQL;

            return DB::select($sql);
        });

        $kingdoms = [];
        $principalities = [];

        foreach ($data as $item) {
            if ($item->parent_kingdom_id != 0) {
                $principalities[] = $item;
            } else {
                $kingdoms[] = $item;
            }
        }

        $events = Cache::remember('events.homepage', $minutes, function () {
            $sql = <<<'SQL'
SELECT
ifnull(k.name,'') kingdom,
k.kingdom_id,
ifnull(p.name,'') park,
e.name event,
c.event_start
FROM ork_event e
LEFT JOIN ork_event_calendardetail c
ON e.event_id = c.event_id
LEFT JOIN ork_park p
ON e.park_id = p.park_id
LEFT JOIN ork_kingdom k
ON e.kingdom_id = k.kingdom_id
WHERE
event_start >= NOW()
ORDER BY event_start ASC
SQL;

            return DB::select($sql);
        });

        $tournaments = Cache::remember('tournaments.homepage', $minutes, function () {
            $sql = <<<'SQL'
SELECT
ifnull(k.name,'') kingdom,
ifnull(p.name,'') park,
e.name event,
e.event_id,
t.name name,
t.date_time date
FROM ork_tournament t
LEFT JOIN ork_park p
ON t.park_id = p.park_id
LEFT JOIN ork_event e
ON t.event_id = e.event_id
LEFT JOIN ork_kingdom k
ON t.kingdom_id = k.kingdom_id
WHERE
t.date_time >= NOW()
ORDER BY t.date_time ASC
SQL;

            return DB::select($sql);
        });

        return view('home.index')->with(['kingdoms' => $kingdoms, 'principalities' => $principalities, 'events' => $events, 'tournaments' => $tournaments, 'pageTitle' => 'Home']);
    }

    public function dashboard()
    {
        $mundane = Mundane::where('email', $this->currentUser->email)->first();

        return view('home.dashboard')->with(
            [
                'mundane'   => $mundane,
                'pageTitle' => 'Dashboard',
            ]
        );
    }
}
