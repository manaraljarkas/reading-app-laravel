<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $countries = Country::paginate(5);

        return response()->json([
            'message' => 'Countries retrieved successfully.',
            'data' => $countries,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request): JsonResponse
    {
        $country = Country::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
        ]);

        return response()->json([
            'message' => 'Country created successfully.',
            'data' => $country,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'message' => 'Country not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Country retrieved successfully.',
            'data' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, string $id): JsonResponse
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'message' => 'Country not found.'
            ], 404);
        }

        $data = $request->only(['name', 'code']);

        if (empty($data)) {
            return response()->json([
                'message' => 'No update data provided.'
            ], 422);
        }

        $country->update($data);

        return response()->json([
            'message' => 'Country updated successfully.',
            'data' => $country->refresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json([
                'message' => 'Country not found.'
            ], 404);
        }

        $country->delete();

        return response()->json([
            'message' => 'Country deleted successfully.',
        ], 200);
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
