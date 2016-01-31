<?php namespace Ork\Model;

class Login extends Model
{

	function __construct()
	{
		parent::__construct();
		$this->Authorization = new APIModel( 'Authorization' );
	}

	function logout( $userid )
	{
		unset( $this->session->user_id );
		unset( $this->session->user_name );
		unset( $this->session->token );
		unset( $this->session->timeout );
	}

	function login( $username, $password )
	{
		$r = $this->Authorization->Authorize( [ 'UserName' => $username, 'Password' => $password, 'Token' => null ] );
		if ( $r[ 'Status' ][ 'Status' ] != 0 ) {
			return $r;
		} else {
			$this->session->user_id = $r[ 'UserId' ];
			$this->session->user_name = $username;
			$this->session->token = $r[ 'Token' ];
			$this->session->timeout = $r[ 'Timeout' ];
			return true;
		}
	}

	function recover_password( $username, $email )
	{
		$r = $this->Authorization->ResetPassword( [ 'UserName' => $username, 'Email' => $email ] );
		if ( $r[ 'Status' ] != 0 ) {
			return $r;
		} else {
			return true;
		}
	}
}
