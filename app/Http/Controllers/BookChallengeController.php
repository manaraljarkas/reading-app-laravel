<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookChallengeRequest;
use App\Http\Requests\UpdateBookChallengeRequest;
use App\Models\BookChallenge;
use App\Models\Book;

class BookChallengeController extends Controller
{
    public function store(StoreBookChallengeRequest $request)
    {
        try {
            $validated = $request->validated();

            Book::findOrFail($validated['book_id']);

            $challenge = BookChallenge::create($validated);

            return response()->json([
                'message' => 'Book challenge created successfully',
                'data' => $challenge
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Book not found'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create challenge',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateBookChallengeRequest $request, $id)
    {
        try {
            $challenge = BookChallenge::findOrFail($id);
            $challenge->update($request->validated());

            return response()->json([
                'message' => 'Book challenge updated successfully',
                'data' => $challenge
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Challenge not found'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update challenge',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
