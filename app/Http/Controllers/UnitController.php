<?php

namespace App\Http\Controllers;

use App\Unit;

class UnitController extends Controller
{
    public function show($id)
    {
        $unit = Unit::findOrFail($id);

        return view('unit.show', ['unit' => $unit, 'pageTitle' => $unit->name]);
    }
}
