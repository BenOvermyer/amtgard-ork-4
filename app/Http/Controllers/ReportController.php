<?php

namespace App\Http\Controllers;

use App\Kingdom;
use Cache;
use DateInterval;
use DateTime;
use DB;

class ReportController extends Controller
{
    public function newbiesForMonth($year, $month)
    {
        $startDate = "$year-$month-01";
        $date = new DateTime("$year-".($month + 1).'-01');
        $date->sub(new DateInterval('P1D'));
        $endDate = $date->format('Y-m-d');

        $newbies = Cache::remember('report_newbies_'.$year.'_'.$month, 120, function () use ($startDate, $endDate) {
            $sql = <<<'SQL'
SELECT
DISTINCT a.mundane_id AS 'id',
c.persona,
p.name AS 'park',
k.name AS 'kingdom'
FROM
(
    SELECT
    ssa.mundane_id,
    SUM(ssa.credits) as 'credits'
    FROM
    ork_attendance ssa
    WHERE
    ssa.date BETWEEN ? AND ?
    GROUP BY ssa.mundane_id
) a LEFT JOIN
(
    SELECT
    ssa.mundane_id,
    SUM(ssa.credits) as 'credits'
    FROM
    ork_attendance ssa
    WHERE
    ssa.date < ?
    GROUP BY ssa.mundane_id
) b ON a.mundane_id = b.mundane_id
LEFT JOIN ork_mundane c ON a.mundane_id = c.mundane_id
LEFT JOIN ork_park p ON c.park_id = p.park_id
LEFT JOIN ork_kingdom k ON c.kingdom_id = k.kingdom_id
WHERE
b.credits IS NULL
ORDER BY
k.name,
p.name,
c.persona
SQL;

            return DB::select($sql, [$startDate, $endDate, $startDate]);
        });

        return view('reports.newbies-by-month', [
            'newbies'   => $newbies,
            'month'     => date_format(new DateTime($startDate), 'F'),
            'year'      => $year,
            'pageTitle' => "$year-$month Newbies",
        ]);
    }

    public function springMuster($kingdomId, $year)
    {
        $kingdom = Kingdom::findOrFail($kingdomId);

        $startDate = "$year-03-01";
        $endDate = "$year-03-31";

        $newbieCountsByPark = Cache::remember('spring_muster_newbies_'.$kingdomId.'_'.$year, 120, function () use ($startDate, $endDate, $kingdomId) {
            $sql = <<<'SQL'
SELECT
p.name AS 'park',
pc.count AS 'count'
FROM
ork_park p
LEFT JOIN
(
    SELECT
    p.park_id,
    COUNT(*) AS 'count'
    FROM
    (
        SELECT
        ssa.mundane_id,
        SUM(ssa.credits) as 'credits'
        FROM
        ork_attendance ssa
        WHERE
        ssa.date BETWEEN ? AND ?
        GROUP BY ssa.mundane_id
    ) a LEFT JOIN
    (
        SELECT
        ssa.mundane_id,
        SUM(ssa.credits) as 'credits'
        FROM
        ork_attendance ssa
        WHERE
        ssa.date < ?
        GROUP BY ssa.mundane_id
    ) b ON a.mundane_id = b.mundane_id
    LEFT JOIN ork_mundane m ON a.mundane_id = m.mundane_id
    LEFT JOIN ork_park p ON m.park_id = p.park_id
    WHERE
    b.credits IS NULL
    AND m.kingdom_id = ?
    AND p.name IS NOT NULL
    GROUP BY p.park_id
) pc ON p.park_id = pc.park_id
WHERE p.kingdom_id = ?
GROUP BY
park
ORDER BY
park
SQL;

            return DB::select($sql, [$startDate, $endDate, $startDate, $kingdomId, $kingdomId]);
        });

        $secondCreditsByPark = Cache::remember('spring_muster_second_credits_'.$kingdomId.'_'.$year, 120, function () use ($startDate, $endDate, $kingdomId) {
            $sql = <<<'SQL'
SELECT
p.name AS 'park',
pc.count AS 'count'
FROM
ork_park p
LEFT JOIN
(
    SELECT
    p.park_id,
    COUNT(*) AS 'count'
    FROM
    (
        SELECT
        ssa.mundane_id,
        SUM(ssa.credits) as 'credits'
        FROM
        ork_attendance ssa
        WHERE
        ssa.date BETWEEN ? AND ?
        GROUP BY ssa.mundane_id
    ) a LEFT JOIN
    (
        SELECT
        ssa.mundane_id,
        SUM(ssa.credits) as 'credits'
        FROM
        ork_attendance ssa
        WHERE
        ssa.date < ?
        GROUP BY ssa.mundane_id
    ) b ON a.mundane_id = b.mundane_id
    LEFT JOIN ork_mundane m ON a.mundane_id = m.mundane_id
    LEFT JOIN ork_park p ON m.park_id = p.park_id
    WHERE
    b.credits = 1
    AND m.kingdom_id = ?
    AND p.name IS NOT NULL
    GROUP BY p.park_id
) pc ON p.park_id = pc.park_id
WHERE p.kingdom_id = ?
GROUP BY
park
ORDER BY
park
SQL;

            return DB::select($sql, [$startDate, $endDate, $startDate, $kingdomId, $kingdomId]);
        });

        $averageAttendanceByWeek = Cache::remember('spring_muster_average_'.$kingdomId.'_'.$year, 120, function () use ($kingdomId, $year) {
            $januaryFirst = "$year-01-01";
            $endOfFebruary = date_format(new DateTime("$year-02-01"), 'Y-m-t');

            $sql = <<<'SQL'
SELECT
p.name AS 'park',
( COUNT(*) / 8 ) AS 'attendance'
FROM
ork_park p
LEFT JOIN ork_mundane m ON p.park_id = m.park_id
LEFT JOIN ork_attendance a ON m.mundane_id = a.mundane_id
WHERE
m.kingdom_id = ?
AND a.date BETWEEN ? AND ?
GROUP BY
park
ORDER BY
park
SQL;

            return DB::select($sql, [$kingdomId, $januaryFirst, $endOfFebruary]);
        });

        return view('reports.spring-muster', [
            'kingdom'   => $kingdom,
            'newbies'   => $newbieCountsByPark,
            'seconds'   => $secondCreditsByPark,
            'averages'  => $averageAttendanceByWeek,
            'pageTitle' => $kingdom->name.' Spring Muster, '.$year,
            'year'      => $year,
        ]);
    }
}
