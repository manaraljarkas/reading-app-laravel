<?php

namespace App\Http\Controllers;

use App\Models\BookSuggestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    public function index()
    {
        $reader = Auth::user();
        $suggestions = BookSuggestion::select('title', 'author_name', 'reader_id', 'created_at')->get();
        $suggestions = $suggestions->map(function ($suggestion) {
            $date_of_suggestion = Carbon::parse($suggestion->created_at);
            return [
                'reader_id' => $suggestion->reader_id,
                'book_title' => $suggestion->title,
                'author' => $suggestion->author_name,
                'date_of_suggestion' => $date_of_suggestion,
            ];
        });

        return response()->json($suggestions);
    }

    public function getSuggestionInfo($suggestionId)
    {
        $admin = Auth::user();
        $suggestion = BookSuggestion::FindOrFail($suggestionId);
        return response()->json([
            'note' => $suggestion->note,
        ]);
    }
    public function deleteSuggestion($suggestionId)
    {
        $user = Auth::user();

        $suggestion = BookSuggestion::find($suggestionId);
        if (!$suggestion) {
            return response()->json(['message' => 'Suggestion not found'], 404);
        }
        $suggestion->delete();
        return response()->json(['message' => 'Suggestion deleted successfully']);
    }

    public function UpdateSuggestion(Request $request, $suggestionId)
    {
        $admin = Auth::user();

        $suggestion = BookSuggestion::FindOrFail($suggestionId);
        $validate = $request->validate([
            'status' => 'required|in:[Pending,Accepted,Denied]',
        ]);

        $suggestion->status = $request->input('status');
        $suggestion->save();
        return response()->json(['status' => $suggestion->status]);
    }
}
