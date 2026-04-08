<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Services\ExtraAttributesService;
use App\Http\Services\ItemService;
use App\Models\Item;
use App\Models\Visitor;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private ItemService $itemService;
    private ExtraAttributesService $extraAttributesService;

    public function __construct(
        ItemService $itemService,
        ExtraAttributesService $extraAttributesService,
    ) {
        $this->itemService = $itemService;
        $this->extraAttributesService = $extraAttributesService;
    }

    /**
     * Display all items related to the given visitor.
     */
    public function index(Visitor $visitor)
    {
        return $this->itemService->getFromVisitor($visitor);
    }

    /**
     * Add a new item to the given visitor.
     * The authenticated volunteer is recorded as the actor in the audit log.
     */
    public function store(StoreItemRequest $request, Visitor $visitor)
    {
        $item = new Item();
        $this->updateItemObjectFromRequest($request, $item);

        /** @var Volunteer|null $actor */
        $actor =
            $request->user() instanceof Volunteer ? $request->user() : null;

        $this->itemService->appendItemToVisitor($item, $visitor, $actor);
    }

    /**
     * Show a specific item of the given visitor.
     *
     * The item is fetched by order of creation and availability. This means that using 0 as the item
     * parameter will return the first item of the visitor, 1 will return the second item and so on.
     */
    public function show(Visitor $visitor, string $item): Item
    {
        return $visitor->items[$item];
    }

    /**
     * Show a specific item.
     */
    public function showById(Item $item)
    {
        return $item;
    }

    /**
     * Update an existing item.
     * The authenticated volunteer is recorded as the actor in the audit log.
     *
     * @param UpdateItemRequest $request The request containing the new item data
     * @param Item $item The old Item object fetched by Laravel
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $this->updateItemObjectFromRequest($request, $item);

        /** @var Volunteer|null $actor */
        $actor =
            $request->user() instanceof Volunteer ? $request->user() : null;

        $this->itemService->save($item, $actor);
    }

    /**
     * Remove an item.
     * The authenticated volunteer is recorded as the actor in the audit log.
     */
    public function destroy(Request $request, Item $item)
    {
        /** @var Volunteer|null $actor */
        $actor =
            $request->user() instanceof Volunteer ? $request->user() : null;

        $this->itemService->delete($item, $actor);
    }

    /**
     * Updates the item object given in parameter with the request data.
     *
     * This method **does not** handle any enforcement the model may have.
     *
     * @param Request $request
     * @param Item    $item
     * @return void
     */
    private function updateItemObjectFromRequest(
        Request $request,
        Item $item,
    ): void {
        // Users may send an integer, so we need to cast it to a float
        $item->castAndSet(
            "weight",
            $request->input("weight", $item->weight ?? null),
        );
        $item->age = $request->input("age", $item->age ?? null);
        $item->name = $request->input("name", $item->name ?? null);
        $item->is_electric = $request->input(
            "is_electric",
            $item->is_electric ?? false,
        );
        $item->brand = $request->input("brand", $item->brand ?? null);
        $this->extraAttributesService->updateAttributes(
            $item,
            $request->input("extra_attributes", $item->extra_attributes ?? []),
        );
    }
}
