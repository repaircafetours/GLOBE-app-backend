<?php

namespace App\Http\Controllers;

use App\Http\Services\ExtraAttributesService;
use App\Http\Services\ItemService;
use App\Models\Item;
use App\Models\Visitor;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    private ItemService $itemService;
    private ExtraAttributesService $extraAttributesService;

    public function __construct(ItemService $itemService, ExtraAttributesService $extraAttributesService) {
        $this->itemService = $itemService;
        $this->extraAttributesService = $extraAttributesService;
    }

    /**
     * Display a listing of the resource for a specific visitor.
     */
    public function index(Visitor $visitor)
    {
        return $this->itemService->getFromVisitor($visitor);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Visitor $visitor)
    {
        $item = new Item();
        $this->updateItemObjectFromRequest($request, $item);
        $this->itemService->appendItemToVisitor($item, $visitor);
    }

    /**
     * Display the specified resource.
     */
    public function show(Visitor $visitor, string $item)
    {
        return $visitor->items[$item];
    }

    public function showById(Item $item)
    {
        return $item;
    }

    /**
     * Update the specified resource in storage.
     * @param Visitor  $visitor is not used, but it is required to make the route work.
     * @param Item  The old Item object fetched by laravel
     */
    public function update(Request $request, Item $item)
    {
        $this->updateItemObjectFromRequest($request, $item);
        $this->itemService->save($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $this->itemService->delete($item);
    }

    /**
     * Updates the item object given in parameter with the request data.
     * 
     * This method **does not** handle any enforcement the model may have
     * @param Request $request
     * @param Item $item
     * @return void
     */
    private function updateItemObjectFromRequest(Request $request, Item $item) {
        // Users may send an integer, so we need to cast it to a float
        $item->castAndSet("weight",  $request->input("weight", $item->weight ?? null));
        $item->age = $request->input("age", $item->age ?? null);
        $item->name = $request->input("name", $item->name ?? null);
        $item->is_electric = $request->input("is_electric", $item->is_electric ?? false);
        $item->brand = $request->input("brand", $item->brand ?? null);
        $this->extraAttributesService->updateAttributes($item, $request->input("extra_attributes", $item->extra_attributes ?? []));
    }
}
