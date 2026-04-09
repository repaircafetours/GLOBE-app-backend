<?php

namespace App\Http\Services;

use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Event;
use App\Models\Item;
use App\Models\Visitor;
use Illuminate\Support\Carbon;

class EventService {


    public function save(Event $event) {
        $event->save();
    }

    public function getEventById($id): Event {
        return Event::find($id);
    }

    public function getAppointments(Event $event) {
        $items = $event->items()->get();
        $res = [];
        $i = 0;
        foreach ($items as $item) {
            $res[$i] = $item->pivot;
            $i++;
        }
        return $res;
    }

    public function addVisitorItemToEvent(Event $event, Item $item, Carbon $appointment_date) {
        $event->items()->attach($item->id, [
            "appointment_date" => $appointment_date
        ]);
        $event->save();
    }

    public function updateVisitorAppointment(
        Event $event,
        Item $item,
        ?Carbon $appointment_date = null,
        ?int $satisfaction_rating = null,
        ?string $comment
    ) {    
        $appointment = $event
            ->items()
            ->wherePivot("event_id", $event->id)
            ->wherePivot("item_id", $item->id)
            ->first();
        if ($appointment) {
            $updateData = [];
            if ($satisfaction_rating !== null) {
                $updateData['satisfaction_rating'] = $satisfaction_rating;
            }
            if ($comment !== null) {
                $updateData['comment'] = $comment;
            }
            if (!empty($updateData)) {
                $event->items()->updateExistingPivot($item->id, $updateData, false);
            }
        }
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