<?php

namespace App\Http\Services;

use App\Http\Services\Logs\ItemLoggerService;
use App\Models\Item;
use App\Models\Visitor;

class ItemService {


    private ItemLoggerService $logger;

    public function __construct(ItemLoggerService $logger) {
        $this->logger = $logger;
    }

    public function save(Item $item) {
        $this->logger->log($item);
        $item->save();
    }

    /**
     * Returns the old version of the current item. If it has not been inserted
     * in the database, returns the same item
     * @param Item $item
     * @return Item The database instance of the requested Item, or a new instance if it does not exists
     */
    public function getFromItem(Item $item): Item {
        if (!$item->id) return new Item();
        return $this->getFromId($item->id);
    }

    public function getFromId(int $id): Item {
        return Item::find($id);
    }

    public function getFromVisitor(Visitor $visitor) {
        return $visitor->items;
    }

    public function getAll() {
        return Item::all();
    }

    public function delete(Item $item) {
        $this->logger->logDelete($item);
        $item->delete();
    }

}