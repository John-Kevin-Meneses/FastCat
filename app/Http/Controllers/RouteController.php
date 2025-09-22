<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;

class RouteController extends Controller
{
    /**
     * Display a listing of all routes.
     */
    public function index()
    {
        return response()->json(Route::all(), 200);
    }

    /**
     * Store a newly created route in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'origin'      => 'required|string|max:255',
            'destination' => 'required|string|max:255',
        ]);

        $route = Route::create($validated);

        return response()->json($route, 201);
    }

    /**
     * Display the specified route.
     */
    public function show(string $id)
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Route not found'], 404);
        }

        return response()->json($route, 200);
    }

    /**
     * Update the specified route in storage.
     */
    public function update(Request $request, string $id)
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Route not found'], 404);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'origin'      => 'sometimes|string|max:255',
            'destination' => 'sometimes|string|max:255',
        ]);

        $route->update($validated);

        return response()->json($route, 200);
    }

    /**
     * Remove the specified route from storage.
     */
    public function destroy(string $id)
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Route not found'], 404);
        }

        $route->delete();

        return response()->json(['message' => 'Route deleted successfully'], 200);
    }
}
