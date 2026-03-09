<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function index()
    {
        return Volunteer::all();
    }

    public function store(Request $request)
    {
        Volunteer::create($request->all());
    }

    public function show(int $volunteer_id)
    {
        return Volunteer::find($volunteer_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Volunteer $volunteer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Volunteer $volunteer)
    {
        //
    }
}
