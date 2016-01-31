<?php namespace Ork;

class Helper
{
    function html_encode( $string )
    {
        return htmlentities( $string, ENT_QUOTES | ENT_HTML5, "ISO-8859-1", false );
    }

    function html_decode( $string )
    {
        return html_entity_decode( $string, ENT_QUOTES | ENT_HTML5, "ISO-8859-1" );
    }

    function trimlen( $text )
    {
        return strlen( trim( $text ) ) > 0;
    }

    function valid_id( $id )
    {
        return is_numeric( $id ) && $id > 0;
    }

    function push_stack( $a, $e )
    {
        array_push( $a, $e );
        return $a;
    }

    function strip_tags_r( $val )
    {
        return is_array( $val ) ?
            array_map( 'strip_tags_r', $val ) :
            strip_tags( $val );
    }

    // Encode a string to URL-safe base64
    function encodeBase64UrlSafe( $value )
    {
        return str_replace( [ '+', '/' ], [ '-', '_' ],
            base64_encode( $value ) );
    }

    // Decode a string from URL-safe base64
    function decodeBase64UrlSafe( $value )
    {
        return base64_decode( str_replace( [ '-', '_' ], [ '+', '/' ],
            $value ) );
    }

    // Sign a URL with a given crypto key
    // Note that this URL must be properly URL-encoded
    function signUrl( $myUrlToSign, $privateKey )
    {
        return $myUrlToSign;
        // parse the url
        $url = parse_url( $myUrlToSign );

        $urlPartToSign = $url[ 'path' ] . "?" . $url[ 'query' ];

        // Decode the private key into its binary format
        $decodedKey = decodeBase64UrlSafe( $privateKey );

        // Create a signature using the private key and the URL-encoded
        // string using HMAC SHA1. This signature will be binary.
        $signature = hash_hmac( "sha1", $urlPartToSign, $decodedKey, true );

        $encodedSignature = encodeBase64UrlSafe( $signature );

        return $myUrlToSign . "&signature=" . $encodedSignature;
    }
}