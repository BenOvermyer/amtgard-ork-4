<?php namespace Ork;

class Administration
{

	function __construct()
	{
		global $DB;
		$this->db = $DB;
		$this->log = new yapo( $this->db, DB_PREFIX . 'log' );
		$this->trace = [ ];
	}

	function PurgeLogs( $Token )
	{
		if ( ( $mundane_id = Ork3::$Lib->authorization->IsAuthorized( $Token ) ) > 0
			&& Ork3::$Lib->authorization->HasAuthority( $mundane_id, AUTH_ADMIN, 0, AUTH_CREATE )
		) {
			$date = date( "Y-m-d H:i:s", time() - 60 );
			$continue = true;
			$total = 0;
			do {
				set_time_limit( 60 );
				$sql = "select count(log_id) as hits from " . DB_PREFIX . "log where action_time < '$date' order by log_id asc limit 50";
				$find = $this->db->query( $sql );
				if ( !$find->size() )
					break;
				if ( $find->hits < 50 )
					$continue = false;
				$total += $find->hits;
				$sql = "delete from " . DB_PREFIX . "log where action_time < '$date' order by log_id asc limit 50";
				$this->db->query( $sql );
			} while ( $continue );
			return Success( $total );
		}
		return NoAuthorization();
	}

	function OptimizeTable( $Token, $Table = null )
	{
		$total = 0;
		if ( ( $mundane_id = Ork3::$Lib->authorization->IsAuthorized( $Token ) ) > 0
			&& Ork3::$Lib->authorization->HasAuthority( $mundane_id, AUTH_ADMIN, 0, AUTH_CREATE )
		) {
			if ( is_null( $Table ) ) {
				$tables = $this->db->query( 'show tables' );

				$t = 'Tables_in_' . DB_DATABASE;
				do {
					set_time_limit( 60 * 60 );
					$this->db->query( 'optimize table "' . $tables->$t . '"' );
					$total++;
				} while ( $tables->next() );
			} else if ( is_array( $Table ) && count( $Table > 0 ) ) {
				foreach ( $Table as $k => $t ) {
					set_time_limit( 60 * 60 );
					$this->db->query( 'optimize table "' . $t . '"' );
					$total++;
				}
			}
			return Success( $total );
		}
		return NoAuthorization();
	}
}