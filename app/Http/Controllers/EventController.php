<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddItemToEventRequest;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Services\ExtraAttributesService;
use App\Models\Event;
use App\Http\Services\EventService;
use App\Models\Item;
use Illuminate\Support\Carbon;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class EventController extends Controller
{

    private EventService $eventService;
    private ExtraAttributesService $extraAttributesService;

    public function __construct(EventService $service, ExtraAttributesService $extraAttributesService)
    {
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
        $event->castAndSet("date", $validated["date"]);
        $event->city = $validated["city"];
        $event->zip_code = $validated["zip_code"];
        $event->address = $validated["address"];
        $extra_attributes = $validated["extra_attributes"] ?? null;
        if ($extra_attributes) {
            $this->extraAttributesService->updateAttributes($event, $extra_attributes);
        }
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
     * Insert a Item in the event
     * 
     * Even though a visitor attends to an event, we use the item to represent them since a visitor may have multiple
     * items but will only bring one to the event.
     * @param Event $event
     * @param Item $item
     * @return void
     */
    public function addNewItemToEvent(AddItemToEventRequest $request, Event $event, Item $item)
    {
        $validated = $request->validated();
        $date = new Carbon($validated["date"]);
        $this->eventService->addVisitorItemToEvent($event, $item, $date);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();
        $event->castAndSet("date", $validated["date"] ?? $event->date);
        $event->city = $validated["city"] ?? $event->city;
        $event->zip_code = $validated["zip_code"] ?? $event->zip_code;
        $event->address = $validated["address"] ?? $event->address;
        $this->extraAttributesService->updateAttributes($event, $validated["extra_attributes"] ?? null);
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

    public function getAppointmentsFromEvent(Event $event) {
        return $this->eventService->getAppointments($event);
    }

    public function updateAppointment(UpdateAppointmentRequest $request, Event $event, Item $item) {
        $validated = $request->validated();
        $date = $validated["date"] ?? null;
        if($date) $date = new Carbon($date);
        $comment = $validated["comment"] ?? null;
        $satisfaction = $validated["satisfaction"] ?? null; 
        $this->eventService->updateVisitorAppointment(
            $event,
            $item,
            $date,
            $satisfaction,
            $comment
        );
    }
}
