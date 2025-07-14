<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getTrips()
    {
        $user = Auth::user();
        $reader = $user->reader;

        if (!$reader) {
            return response()->json([
                'message' => 'Reader profile not found.',
            ], 404);
        }

        $books = $reader->books()->with('author.country')->get();

        $trips = $books
            ->filter(fn($book) => $book->author && $book->author->country && $book->author->country->code)
            ->groupBy(fn($book) => $book->author->country->code)
            ->map(fn($group, $code) => [
                'country_code' => $code,
                'book_count' => $group->count(),
            ])->values();

        return response()->json([
            'message' => 'Trips data fetched successfully.',
            'data' => $trips,
        ], 200);
    }
}
