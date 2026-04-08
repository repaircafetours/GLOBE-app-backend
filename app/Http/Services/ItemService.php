<?php

namespace App\Http\Services;

use App\Http\Services\Logs\ItemLoggerService;
use App\Models\Item;
use App\Models\Visitor;
use App\Models\Volunteer;

class ItemService
{
    private ItemLoggerService $logger;

    public function __construct(ItemLoggerService $logger)
    {
        $this->logger = $logger;
    }

    public function save(Item $item, ?Volunteer $actor = null): void
    {
        $isNew = !$item->exists;

        if ($isNew) {
            $item->save();
            $this->logger->log($item, new Item(), $actor);
        } else {
            // Capture the old state BEFORE overwriting it with save()
            $old = $this->getFromItem($item) ?? new Item();
            $item->save();
            $this->logger->log($item, $old, $actor);
        }
    }

    public function appendItemToVisitor(
        Item $item,
        Visitor $visitor,
        ?Volunteer $actor = null,
    ): void {
        $visitor->items()->save($item);
        // New item: no previous state
        $this->logger->log($item, new Item(), $actor);
    }

    /**
     * Returns the old version of the current item. If it has not been inserted
     * in the database, returns a new empty instance.
     *
     * @param Item $item
     * @return Item The database instance of the requested Item, or a new instance if it does not exist
     */
    public function getFromItem(Item $item): Item
    {
        if (!$item->id) {
            return new Item();
        }
        return $this->getFromId($item->id);
    }

    public function getFromId(int $id): Item
    {
        return Item::find($id);
    }

    public function getFromVisitor(Visitor $visitor)
    {
        return $visitor->items;
    }

    public function getAll()
    {
        return Item::all();
    }

    public function delete(Item $item, ?Volunteer $actor = null): void
    {
        $this->logger->logDelete($item, $actor);
        $item->delete();
    }
}
