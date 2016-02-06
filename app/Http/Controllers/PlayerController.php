<?php namespace App\Http\Controllers;

use App\Mundane;
use DB;

class PlayerController extends Controller
{
    public function show( $id )
    {
        $player = Mundane::findOrFail( $id );

        $sql = <<<SQL
SELECT
c.class_id,
c.active,
c.name as class_name,
COUNT(a.week) as weeks,
SUM(a.attendances) as attendances,
SUM(a.credits) as credits,
cr.class_reconciliation_id,
cr.reconciled
FROM ork_class c
LEFT JOIN
(
  SELECT
  ssa.class_id,
  COUNT(ssa.attendance_id) as attendances,
  SUM(ssa.credits) as credits,
  week(ssa.date, 6) as week
	FROM
	(
    SELECT
    min(killdupe.attendance_id) as attendance_id
    FROM ork_attendance killdupe
    WHERE killdupe.mundane_id = ? GROUP BY killdupe.date
  ) kd
	LEFT JOIN ork_attendance ssa ON ssa.attendance_id = kd.attendance_id
	WHERE
	ssa.mundane_id = ?
	GROUP BY ssa.class_id, ssa.date
) a ON a.class_id = c.class_id
LEFT JOIN ork_class_reconciliation cr ON cr.class_id = c.class_id AND cr.mundane_id = ?
WHERE c.active = 1
GROUP BY c.class_id
SQL;
        $classes = DB::select( $sql, [ $id, $id, $id ] );

        $sql = <<<SQL
SELECT
a.*,
c.name as class_name,
p.name as park_name,
k.name as kingdom_name,
e.name as event_name,
e.park_id as event_park_id,
e.kingdom_id as event_kingdom_id,
ep.name as event_park_name,
ek.name as event_kingdom_name
FROM ork_attendance a
LEFT JOIN ork_park p on a.park_id = p.park_id
LEFT JOIN ork_kingdom k on a.kingdom_id = k.kingdom_id
LEFT JOIN ork_class c on a.class_id = c.class_id
LEFT JOIN ork_event e on a.event_id = e.event_id
LEFT JOIN ork_park ep on e.park_id = ep.park_id
LEFT JOIN ork_kingdom ek on e.kingdom_id = ek.kingdom_id
WHERE a.mundane_id = ?
ORDER BY a.date DESC
SQL;
        $attendance = DB::select( $sql, [ $id ] );

        return view( 'player.show', [ 'player' => $player, 'classes' => $classes, 'attendance' => $attendance, 'pageTitle' => $player->persona ] );
    }
}