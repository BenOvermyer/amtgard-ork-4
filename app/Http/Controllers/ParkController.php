<?php namespace App\Http\Controllers;

use Cache;
use App\Park;

class ParkController extends Controller
{
    public function show( $id )
    {
        $park = Cache::remember( 'park.' . $id . '.show', config( 'cache.general' ), function () use ( $id ) {
            return Park::findOrFail( $id );
        } );

        return view( 'park.show' )->with( [ 'park' => $park, 'pageTitle' => $park->name ] );
    }
}