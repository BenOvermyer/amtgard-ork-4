<?php namespace Ork\Model;

class Unit extends Model
{

	function __construct()
	{
		parent::__construct();
		$this->Unit = new APIModel( 'Unit' );
		$this->Report = new APIModel( 'Report' );
		$this->Authorization = new APIModel( 'Authorization' );
		$this->Heraldry = new APIModel( 'Heraldry' );
	}

	function merge( $request )
	{
		return $this->Unit->MergeUnits( $request );
	}

	function convert_to_household( $unit_id )
	{
		return $this->Unit->ConvertToHousehold( [
			'Token'  => $this->session->token,
			'UnitId' => $unit_id,
		] );
	}

	function create_unit( $request )
	{
		return $this->Unit->CreateUnit( $request );
	}

	function get_heraldry( $unit_id )
	{
		return $this->Heraldry->GetHeraldryUrl( [ 'Type' => 'Unit', 'Id' => $unit_id ] );
	}

	function set_unit_details( $request )
	{
		return $this->Unit->SetUnit( $request );
	}

	function add_unit_auth( $request )
	{
		logtrace( "add_unit_auth()", $request );
		$this->Authorization->AddAuthorization( $request );
	}

	function set_unit_member( $request )
	{
		logtrace( "set_unit_member()", $request );
		return $this->Unit->SetMember( $request );
	}

	function add_unit_member( $request )
	{
		return $this->Unit->AddMember( $request );
	}

	function retire_unit_member( $request )
	{
		return $this->Unit->RetireMember( $request );
	}

	function remove_unit_member( $request )
	{
		return $this->Unit->RemoveMember( $request );
	}

	function del_unit_auth( $request )
	{
		logtrace( "del_unit_auth()", $request );
		$this->Authorization->RemoveAuthorization( $request );
	}

	function get_unit_list( $request )
	{
		return $this->Report->UnitSummary( $request );
	}

	function get_unit( $unit_id )
	{
		return $this->Unit->GetUnit( [ 'UnitId' => $unit_id ] );
	}

	function get_unit_details( $unit_id )
	{
		return [
			'Details'        => $this->Unit->GetUnit( [ 'UnitId' => $unit_id ] ),
			'Members'        => $this->Report->GetPlayerRoster( [ 'Type' => AUTH_UNIT, 'Id' => $unit_id ] ),
			'Authorizations' => $this->Report->GetAuthorizations( [ 'Type' => AUTH_UNIT, 'Id' => $unit_id, 'Token' => $this->session->token ] ),
		];
	}

}
