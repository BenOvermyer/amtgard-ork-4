<?php namespace Ork\System;

use Controller;

class AJAXController extends Controller
{

    public function __construct( $request = null, $action = null )
    {
        parent::__construct( $request, $action );
    }

    public function index( $action = null )
    {

    }

    public function view()
    {
        return json_encode( $this->data );
    }
}

