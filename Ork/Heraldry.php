<?php namespace Ork;

class Heraldry extends Ork3
{

	public function __construct()
	{
		parent::__construct();
		$this->mundane = new yapo( $this->db, DB_PREFIX . 'mundane' );
		$this->kingdom = new yapo( $this->db, DB_PREFIX . 'kingdom' );
		$this->park = new yapo( $this->db, DB_PREFIX . 'park' );
		$this->unit = new yapo( $this->db, DB_PREFIX . 'unit' );
		$this->event = new yapo( $this->db, DB_PREFIX . 'event' );
	}

	public function GetHeraldry( $request )
	{
		$response = [ 'Heraldry' => '' ];
		switch ( $request[ 'Type' ] ) {
			case 'Player':
				$response[ 'Heraldry' ] = base64_encode( file_get_contents( DIR_PLAYER_HERALDRY . sprintf( '%06d.jpg', $request[ 'Id' ] ) ) );
				break;
		}
		return $response;
	}

	public function GetHeraldryUrl( $request )
	{
		$response = [ 'Url' => '' ];
		switch ( $request[ 'Type' ] ) {
			case 'Player':
				$response[ 'Url' ] = HTTP_PLAYER_HERALDRY . sprintf( '%06d.jpg', $request[ 'Id' ] );
				break;
			case 'Park':
				$response[ 'Url' ] = HTTP_PARK_HERALDRY . sprintf( '%05d.jpg', $request[ 'Id' ] );
				break;
			case 'Kingdom':
				$response[ 'Url' ] = HTTP_KINGDOM_HERALDRY . sprintf( '%04d.jpg', $request[ 'Id' ] );
				break;
			case 'Unit':
				$response[ 'Url' ] = HTTP_UNIT_HERALDRY . sprintf( '%05d.jpg', $request[ 'Id' ] );
				break;
			case 'Event':
				$response[ 'Url' ] = HTTP_EVENT_HERALDRY . sprintf( '%05d.jpg', $request[ 'Id' ] );
				break;
		}
		return $response;
	}

	public function SetPlayerHeraldry( $request )
	{
		$mundane = Ork3::$Lib->player->player_info( $request[ 'MundaneId' ] );

		if ( ( ( $mundane_id = Ork3::$Lib->authorization->IsAuthorized( $request[ 'Token' ] ) ) > 0
				&& Ork3::$Lib->authorization->HasAuthority( $mundane_id, AUTH_PARK, $mundane[ 'ParkId' ], AUTH_EDIT ) )
			|| $mundane_id == $request[ 'MundaneId' ]
		) {
			$this->mundane->clear();
			$this->mundane->mundane_id = $request[ 'MundaneId' ];
			if ( $this->mundane->find() ) {
				$request = $this->fetch_url_heraldry( $request );
				$this->store_heraldry( $request, DIR_PLAYER_HERALDRY, 6, 'mundane' );
				$this->mundane->save();
				return Success();
			} else {
				return InvalidParameter();
			}
		} else {
			return NoAuthorization();
		}
	}

	private function store_heraldry( $request, $path, $img_len, $table )
	{
		if ( strlen( $request[ 'Heraldry' ] ) > 0 && strlen( $request[ 'Heraldry' ] ) < 465000 && Common::supported_mime_types( $request[ 'HeraldryMimeType' ] ) ) {
			$heraldry = @imagecreatefromstring( base64_decode( $request[ 'Heraldry' ] ) );
			if ( $heraldry !== false ) {
				$src_id = ucwords( $table ) . 'Id';
				if ( file_exists( $path . ( sprintf( "%0" . $img_len . "d", $request[ $src_id ] ) ) . '.jpg' ) )
					unlink( $path . ( sprintf( "%0" . $img_len . "d", $request[ $src_id ] ) ) . '.jpg' );
				imagejpeg( $heraldry, $path . ( sprintf( "%0" . $img_len . "d", $request[ $src_id ] ) ) . '.jpg' );
				$this->$table->has_heraldry = 1;
			}
		}
	}

	private function fetch_url_heraldry( $request )
	{
		if ( strlen( $request[ 'HeraldryUrl' ] ) > 0 && Common::url_exists( $request[ 'HeraldryUrl' ] ) ) {
			if ( $this->url_file_size( $request[ 'HeraldryUrl' ] ) < 465000 ) {
				$request[ 'Heraldry' ] = base64_encode( file_get_contents( $request[ 'HeraldryUrl' ] ) );
				$request[ 'HeraldryMimeType' ] = Common::exif_to_mime( @exif_imagetype( $tmp_file ), $request[ 'HeraldryUrl' ] );
			}
		}
		return $request;
	}

	public function SetPrincipalityHeraldry( $request )
	{
		$request[ 'KingdomId' ] = $request[ 'PrincipalityId' ];
		$this->SetKingdomHeraldry( $request );
	}

	public function url_file_size( $remoteFile )
	{
		$ch = curl_init( $remoteFile );
		curl_setopt( $ch, CURLOPT_NOBODY, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true ); //not necessary unless the file redirects (like the PHP example we're using here)
		$data = curl_exec( $ch );
		curl_close( $ch );
		if ( $data === false ) {
			echo 'cURL failed';
			exit;
		}

		$contentLength = 0;
		if ( preg_match( '/Content-Length: (\d+)/', $data, $matches ) ) {
			$contentLength = (int)$matches[ 1 ];
		}

		return $contentLength;
	}

	public function SetKingdomHeraldry( $request )
	{
		if ( ( $mundane_id = Ork3::$Lib->authorization->IsAuthorized( $request[ 'Token' ] ) ) > 0
			&& Ork3::$Lib->authorization->HasAuthority( $mundane_id, AUTH_KINGDOM, $request[ 'KingdomId' ], AUTH_EDIT )
		) {
			$this->kingdom->clear();
			$this->kingdom->kingdom_id = $request[ 'KingdomId' ];
			if ( $this->kingdom->find() ) {
				$request = $this->fetch_url_heraldry( $request );
				$this->store_heraldry( $request, DIR_KINGDOM_HERALDRY, 4, 'kingdom' );
				$this->kingdom->save();
				return Success();
			} else {
				return InvalidParameter();
			}
		} else {
			return NoAuthorization();
		}
	}

	public function SetParkHeraldry( $request )
	{

		if ( ( $mundane_id = Ork3::$Lib->authorization->IsAuthorized( $request[ 'Token' ] ) ) > 0
			&& Ork3::$Lib->authorization->HasAuthority( $mundane_id, AUTH_PARK, $request[ 'ParkId' ], AUTH_EDIT )
		) {
			$this->park->clear();
			$this->park->park_id = $request[ 'ParkId' ];
			if ( $this->park->find() ) {
				$request = $this->fetch_url_heraldry( $request );
				$this->store_heraldry( $request, DIR_PARK_HERALDRY, 5, 'park' );
				$this->park->save();
				return Success();
			} else {
				return InvalidParameter();
			}
		} else {
			return NoAuthorization();
		}
	}

	public function SetUnitHeraldry( $request )
	{
		if ( ( $mundane_id = Ork3::$Lib->authorization->IsAuthorized( $request[ 'Token' ] ) ) > 0
			&& Ork3::$Lib->authorization->HasAuthority( $mundane_id, AUTH_UNIT, $request[ 'UnitId' ], AUTH_EDIT )
		) {
//			logtrace("SetUnitHeraldry() :1", $request);
			$this->unit->clear();
			$this->unit->unit_id = $request[ 'UnitId' ];
			if ( $this->unit->find() ) {
				$request = $this->fetch_url_heraldry( $request );
				$this->store_heraldry( $request, DIR_UNIT_HERALDRY, 5, 'unit' );
				$this->unit->save();
				return Success();
			} else {
				return InvalidParameter();
			}
		} else {
			return NoAuthorization();
		}
	}

	public function SetEventHeraldry( $request )
	{
		if ( ( $mundane_id = Ork3::$Lib->authorization->IsAuthorized( $request[ 'Token' ] ) ) > 0
			&& Ork3::$Lib->authorization->HasAuthority( $mundane_id, AUTH_EVENT, $request[ 'EventId' ], AUTH_EDIT )
		) {
			$this->event->clear();
			$this->event->event_id = $request[ 'EventId' ];
			if ( $this->event->find() ) {
				$request = $this->fetch_url_heraldry( $request );
				$this->store_heraldry( $request, DIR_EVENT_HERALDRY, 5, 'event' );
				$this->event->save();
				return Success();
			} else {
				return InvalidParameter();
			}
		} else {
			return NoAuthorization();
		}
	}

}
