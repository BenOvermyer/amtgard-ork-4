<?php namespace Ork\Model;

class Player extends Model
{

	function __construct()
	{
		parent::__construct();
		$this->Player = new APIModel( 'Player' );
		$this->Award = new APIModel( 'Award' );
	}

	function remove_note( $request )
	{
		return $this->Player->RemoveNote( $request );
	}

	function get_notes( $id )
	{
		return $this->Player->GetNotes( [ 'MundaneId' => $id ] );
	}

	function update_class_reconciliation( $request )
	{
		return $this->Player->SetPlayerReconciledCredits( $request );
	}

	function fetch_player( $mundane_id )
	{
		$player = $this->Player->GetPlayer( [ 'MundaneId' => $mundane_id, 'Token' => $this->session->token ] );
		if ( $player[ 'Status' ][ 'Status' ] != 0 ) return false;
		$player = $player[ 'Player' ];
		return $player;
	}

	function fetch_player_details( $mundane_id )
	{
		$awards = $this->Player->AwardsForPlayer( [ 'MundaneId' => $mundane_id ] );
		if ( $awards[ 'Status' ][ 'Status' ] != 0 ) return $awards;
		$attendance = $this->Player->AttendanceForPlayer( [ 'MundaneId' => $mundane_id ] );
		if ( $attendance[ 'Status' ][ 'Status' ] != 0 ) return $attendance;
		$classes = $this->Player->GetPlayerClasses( [ 'MundaneId' => $mundane_id ] );
		if ( $classes[ 'Status' ][ 'Status' ] != 0 ) return $classes;
		$details = [ 'Awards' => $awards[ 'Awards' ], 'Attendance' => $attendance[ 'Attendance' ], 'Classes' => $classes[ 'Classes' ] ];
		return $details;
	}

	function delete_player_award( $request )
	{
		return $this->Player->RemoveAward( $request );
	}

	function add_player_award( $request )
	{
		return $this->Player->AddAward( $request );
	}

	function update_player_award( $request )
	{
		return $this->Player->UpdateAward( $request );
	}

	function update_player( $request )
	{
		return $this->Player->UpdatePlayer( $request );
	}

	function set_ban( $request )
	{
		$r = $this->Player->SetBan( $request );
		return $r;
	}

	/*
	function create_player($request) {
		$r = $this->Player->CreatePlayer($request);
		return $r;
	}
	*/
	function move_player( $request )
	{
		$r = $this->Player->MovePlayer( $request );
		return $r;
	}

	function merge_player( $request )
	{
		$r = $this->Player->MergePlayer( $request );
		return $r;
	}
}
