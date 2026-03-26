<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Services\ExtraAttributesService;
use App\Models\Event;
use App\Http\Services\EventService;

class EventController extends Controller
{

    private EventService $eventService;
    private ExtraAttributesService $extraAttributesService;

    public function __construct(EventService $service, ExtraAttributesService $extraAttributesService) {
        $this->eventService = $service;
        $this->extraAttributesService = $extraAttributesService;
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
    public function store(CreateEventRequest $request)
    {
        $validated = $request->validated();
        $event = new Event();
        $event->date = $validated["date"];
        $event->city = $validated["city"];
        $event->zip_code = $validated["zip_code"];
        $event->address = $validated["address"];
        $this->extraAttributesService->updateAttributes($event, $validated["extra_attributes"] ?? []);
        $this->eventService->save($event);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        return $event;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();
        $event->date = $validated["date"] ?? $event->date;
        $event->city = $validated["city"] ?? $event->city;
        $event->zip_code = $validated["zip_code"] ?? $event->zip_code;
        $event->address = $validated["address"] ?? $event->address;
        $this->extraAttributesService->updateAttributes($event, $validated["extra_attributes"] ?? []);
        $this->eventService->save($event);
    }

    /**
     * Deletes the specified event from database.
     * 
     * This endpoint will not fail if the event doesn't exists
     */
    public function destroy(Event $event)
    {
        $this->eventService->delete($event);
    }
}
