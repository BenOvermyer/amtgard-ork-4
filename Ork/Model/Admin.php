<?php namespace Ork\Model;

class Admin extends Model
{

	function __construct()
	{
		parent::__construct();
		$this->Kingdom = new APIModel( 'Kingdom' );
		$this->Report = new APIModel( 'Report' );
	}

	function get_kingdom_name( $kingdom_id )
	{
		$r = $this->Kingdom->GetKingdomShortInfo( [ 'KingdomId' => $kingdom_id ] );
		return $r[ 'KingdomInfo' ][ 'KingdomName' ];
	}

	function get_park_summary( $kingdom_id )
	{
		return $this->Report->GetKingdomParkAverages( [ 'KingdomId' => $kingdom_id ] );
	}

}
