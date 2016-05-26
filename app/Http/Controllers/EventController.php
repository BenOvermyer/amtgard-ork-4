<?php

namespace App\Http\Controllers;

use App\EventCalendarDetails;
use Cache;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Cache::remember('event.'.$id.'.show', config('cache.general'), function () use ($id) {
            return EventCalendarDetails::findOrFail($id);
        });

        return view('event.show', ['event' => $event, 'pageTitle' => $event->event->name]);
    }
}
