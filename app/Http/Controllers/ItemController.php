<?php

namespace App\Http\Controllers;

use App\Http\Services\ItemService;
use App\Models\Item;
use App\Models\Visitor;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    private ItemService $itemService;

    public function __construct(ItemService $itemService) {
        $this->itemService = $itemService;
    }

    /**
     * Display a listing of the resource.
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return $item;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $this->itemService->delete($item);
    }

    private function updateItemObjectFromRequest(Request $request, Item $item) {
        $item->weight = $request->input("weight", $item->weight);
        $item->age = $request->input("age", $item->age);
        $item->name = $request->input("name", $item->name);
        $item->is_electric = $request->input("is_electric", $item->is_electric);
        $item->brand = $request->input("brand", $item->brand);
        $item->castAndSet("extra_attributes", $request->input("extra_attributes", $item->extra_attributes));
    }
}
