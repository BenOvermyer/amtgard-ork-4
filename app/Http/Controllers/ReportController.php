<?php namespace App\Http\Controllers;

use Cache;
use DateTime;
use DateInterval;
use DB;

class ReportController extends Controller {

    public function newbiesForMonth( $year, $month ) {

        $startDate = "$year-$month-01";
        $date = new DateTime("$year-" . ( $month + 1 ) . "-01");
        $date->sub( new DateInterval( 'P1D' ) );
        $endDate = $date->format( 'Y-m-d' );

        $newbies = Cache::remember( 'report_newbies_' . $year . '_' . $month, 120, function () use ( $startDate, $endDate ) {
            $sql = <<<SQL
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

            return DB::select( $sql, [ $startDate, $endDate, $startDate ] );
        } );

        return view( 'reports.newbies-by-month', [
            'newbies' => $newbies,
            'month' => date_format( new DateTime( $startDate ), 'F' ),
            'year' => $year,
            'pageTitle' => "$year-$month Newbies",
        ] );
    }
}