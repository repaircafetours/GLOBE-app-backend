<?php

namespace App\Http\Controllers;

use App\Models\Event;
use EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{

    private EventService $eventService;

    public function __construct(EventService $service) {
        $this->eventService = $service;
    }

    /**
     * Returns a list of all events in database
     */
    public function index()
    {
        return $this->eventService->getAll();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
