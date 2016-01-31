<?php namespace Ork\Controller;

class Award extends Controller
{

	public function __construct( $call = null, $id = null )
	{
		parent::__construct( $call, $id );

		$this->load_model( 'Player' );
		$this->load_model( 'Park' );
		$this->load_model( 'Kingdom' );
		$params = explode( '/', $id );
		$id = $params[ 0 ];

		switch ( $call ) {
			case 'park':
				$park_info = $this->Park->get_park_info( $id );
				$this->park_info = $park_info;
				$this->session->park_name = $park_info[ 'ParkInfo' ][ 'ParkName' ];
				$this->session->park_id = $park_info[ 'ParkInfo' ][ 'ParkId' ];
				$this->data[ 'menu' ][ 'park' ] = [ 'url' => UIR . 'Park/index/' . $this->session->park_id, 'display' => $this->session->park_name ];
				$id = $park_info[ 'KingdomInfo' ][ 'KingdomId' ];
			case 'kingdom':
				$kingdom_info = $this->Kingdom->get_kingdom_details( $id );
				$this->kingdom_info = $kingdom_info;
				$this->session->kingdom_id = $kingdom_info[ 'KingdomInfo' ][ 'KingdomId' ];
				$this->session->kingdom_name = $kingdom_info[ 'KingdomInfo' ][ 'KingdomName' ];
				$this->data[ 'menu' ][ 'kingdom' ] = [ 'url' => UIR . 'Kingdom/index/' . $this->session->kingdom_id, 'display' => $this->session->kingdom_name ];
				$this->data[ 'KingdomId' ] = $id;
		}
		$this->data[ 'Call' ] = $call;
	}

	public function park( $id )
	{
		$params = explode( '/', $id );
		$id = $params[ 0 ];
		if ( count( $params ) > 1 )
			$action = $params[ 1 ];

		if ( strlen( $action ) > 0 ) {
			$this->request->save( 'Award_addawards', true );
			$r = [ 'Status' => 0 ];
			if ( !isset( $this->session->user_id ) ) {
				header( 'Location: ' . UIR . "Login/login/Award/park/$id" );
			} else {
				switch ( $action ) {
					case 'addaward':
						if ( !valid_id( $this->request->Award_addawards->MundaneId ) ) {
							$this->data[ 'Error' ] = 'You must choose a recipient. Award not added!';
							break;
						}
						if ( !valid_id( $this->request->Award_addawards->AwardId ) ) {
							$this->data[ 'Error' ] = 'You must choose an award. Award not added!';
							break;
						}
						if ( !valid_id( $this->request->Award_addawards->GivenById ) ) {
							$this->data[ 'Error' ] = 'Who gave this award? Award not added!';
							break;
						}
						$r = $this->Player->add_player_award( [
							'Token'          => $this->session->token,
							'RecipientId'    => $this->request->Award_addawards->MundaneId,
							'KingdomAwardId' => $this->request->Award_addawards->AwardId,
							'Rank'           => $this->request->Award_addawards->Rank,
							'Date'           => $this->request->Award_addawards->Date,
							'GivenById'      => $this->request->Award_addawards->GivenById,
							'Note'           => $this->request->Award_addawards->Note,
							'ParkId'         => valid_id( $this->request->Award_addawards->ParkId ) ? $this->request->Award_addawards->ParkId : 0,
							'KingdomId'      => valid_id( $this->request->Award_addawards->KingdomId ) ? $this->request->Award_addawards->KingdomId : 0,
							'EventId'        => valid_id( $this->request->Award_addawards->EventId ) ? $this->request->Award_addawards->EventId : 0,
						] );
						break;
				}
				if ( $r[ 'Status' ] == 0 ) {
					$this->data[ 'Message' ] = 'Award recorded for ' . $this->request->Award_addawards->GivenTo;
					$this->request->clear( 'Player_index' );
					unset( $_REQUEST[ 'MundaneId' ] );
					unset( $_REQUEST[ 'AwardId' ] );
					unset( $_REQUEST[ 'Rank' ] );
					unset( $_REQUEST[ 'Note' ] );
					unset( $_REQUEST[ 'GivenTo' ] );
					$this->request->save( 'Award_addawards', true );
				} else if ( $r[ 'Status' ] == 5 ) {
					header( 'Location: ' . UIR . "Login/login/Award/park/$id" );
				} else {
					$this->data[ 'Error' ] = $r[ 'Error' ] . ':<p>' . $r[ 'Detail' ];
				}
			}
		}

		$this->template = 'Award_addawards.tpl';
		if ( $this->request->exists( 'Award_addawards' ) ) {
			$this->data[ 'Award_addawards' ] = $this->request->Award_addawards->Request;
		}
		$this->data[ 'AwardOptions' ] = $this->Award->fetch_award_option_list( $this->session->kingdom_id );
		$this->data[ 'Id' ] = $id;
	}

	public function kingdom( $id )
	{
		$params = explode( '/', $id );
		$id = $params[ 0 ];
		if ( count( $params ) > 1 )
			$action = $params[ 1 ];

		if ( strlen( $action ) > 0 ) {
			$this->request->save( 'Award_addawards', true );
			$r = [ 'Status' => 0 ];
			if ( !isset( $this->session->user_id ) ) {
				header( 'Location: ' . UIR . "Login/login/Award/kingdom/$id" );
			} else {
				switch ( $action ) {
					case 'addaward':
						if ( !valid_id( $this->request->Award_addawards->MundaneId ) ) {
							$this->data[ 'Error' ] = 'You must choose a recipient. Award not added!';
							break;
						}
						if ( !valid_id( $this->request->Award_addawards->AwardId ) ) {
							$this->data[ 'Error' ] = 'You must choose an award. Award not added!';
							break;
						}
						if ( !valid_id( $this->request->Award_addawards->GivenById ) ) {
							$this->data[ 'Error' ] = 'Who gave this award? Award not added!';
							break;
						}
						$r = $this->Player->add_player_award( [
							'Token'          => $this->session->token,
							'RecipientId'    => $this->request->Award_addawards->MundaneId,
							'KingdomAwardId' => $this->request->Award_addawards->AwardId,
							'Rank'           => $this->request->Award_addawards->Rank,
							'Date'           => $this->request->Award_addawards->Date,
							'GivenById'      => $this->request->Award_addawards->GivenById,
							'Note'           => $this->request->Award_addawards->Note,
							'ParkId'         => valid_id( $this->request->Award_addawards->ParkId ) ? $this->request->Award_addawards->ParkId : 0,
							'KingdomId'      => valid_id( $this->request->Award_addawards->KingdomId ) ? $this->request->Award_addawards->KingdomId : 0,
							'EventId'        => valid_id( $this->request->Award_addawards->EventId ) ? $this->request->Award_addawards->EventId : 0,
						] );
						break;
				}
				if ( $r[ 'Status' ] == 0 ) {
					$this->data[ 'Message' ] = 'Award recorded for ' . $this->request->Award_addawards->GivenTo;
					$this->request->clear( 'Player_index' );
					unset( $_REQUEST[ 'MundaneId' ] );
					unset( $_REQUEST[ 'AwardId' ] );
					unset( $_REQUEST[ 'Rank' ] );
					unset( $_REQUEST[ 'Note' ] );
					unset( $_REQUEST[ 'GivenTo' ] );
					$this->request->save( 'Award_addawards', true );
				} else if ( $r[ 'Status' ] == 5 ) {
					header( 'Location: ' . UIR . "Login/login/Award/kingdom/$id" );
				} else {
					$this->data[ 'Error' ] = $r[ 'Error' ] . ':<p>' . $r[ 'Detail' ];
				}
			}
		}

		$this->template = 'Award_addawards.tpl';
		if ( $this->request->exists( 'Award_addawards' ) ) {
			$this->data[ 'Award_addawards' ] = $this->request->Award_addawards->Request;
		}
		$this->data[ 'AwardOptions' ] = $this->Award->fetch_award_option_list( $this->session->kingdom_id );
		$this->data[ 'Id' ] = $id;
	}
}
