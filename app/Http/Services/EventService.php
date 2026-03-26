<?php

use App\Models\Event;
use App\Models\Item;
use App\Models\Visitor;

class EventService {


    public function save(Event $event) {
        $event->save();
    }

    public function getEventById($id): Event {
        return Event::find($id);
    }

    public function addVisitorItemToEvent(Event $event, Item $item) {
        $event->items()->save($item);
        $event->save();
    }

    /**
     * Returns the old version of the current event. If it has not been inserted
     * in the database, returns the same event
     * @param Event $event
     * @return Event The database instance of the requested Event, or a new instance if it does not exists
     */
    public function getFromEvent(Event $event): Event {
        if(!$event->id) return new Event();
        return Event::find($event->id);
    }

    public function getAll() {
        return Event::all();
    }

    public function delete(Event $event) {
        $event->delete();
    }


}