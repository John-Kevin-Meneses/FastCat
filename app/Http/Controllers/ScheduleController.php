<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    /**
     * Display a listing of all schedules.
     */
    public function index()
    {
        return response()->json(Schedule::with('route')->get(), 200);
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id'       => 'required|exists:routes,id',
            'departure_time' => 'required|date',
            'arrival_time'   => 'nullable|date|after:departure_time',
        ]);

        $schedule = Schedule::create($validated);

        return response()->json($schedule, 201);
    }

    /**
     * Display the specified schedule.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with('route')->find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        return response()->json($schedule, 200);
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $validated = $request->validate([
            'route_id'       => 'sometimes|exists:routes,id',
            'departure_time' => 'sometimes|date',
            'arrival_time'   => 'nullable|date|after:departure_time',
        ]);

        $schedule->update($validated);

        return response()->json($schedule, 200);
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully'], 200);
    }
}
