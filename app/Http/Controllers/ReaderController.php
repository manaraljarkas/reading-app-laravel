<?php

namespace App\Http\Controllers;

use App\Models\Reader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $readers = Reader::select('id', 'first_name', 'picture', 'user_id')
            ->with('user')
            ->paginate(5)
            ->through(function ($reader) {
                return [
                    'id' => $reader->id,
                    'name' => $reader->first_name,
                    'email' => $reader->user?->email ?? 'no user attached',
                    'picture' =>  $reader->picture,
                ];
            });
        return response()->json(
            $readers);
    }

    public function show($readerId)
    {
        $reader = Auth::user();
        // Get the reader information
        $readerinfo = Reader::select('first_name', 'picture', 'total_points', 'bio', 'nickname')->where('id', '=', $readerId)->first();

        if (!$readerinfo) {
            return response()->json([
                'success' => false,
                'message' => 'Reader not found',
                'data' => null
            ], 404);
        }
        $CountService = new \App\Services\CountService($readerId);

        return response()->json([
            'success' => true,
            'message' => 'Reader profile retrieved successfully.',
            'data' => [
                'name' => $readerinfo->first_name,
                'nickname' => $readerinfo->nickname,
                'bio' => $readerinfo->bio,
                'total_points' => $readerinfo->total_points,
                'picture' => $readerinfo->picture,
                'number_of_challenges' => $CountService->countChallenges(),
                'number_of_books' => $CountService->countBooks(),
                'number_of_countries' => $CountService->countCountries(),
                'number_of_badges' => $CountService->countBadges(),
            ]
        ]);
    }

    public function destroy($readerId)
    {
        $user = Auth::user();
        $reader = Reader::find($readerId);

        if (!$reader) {
            return response()->json(['message' => 'Reader not found'], 404);
        }
        $reader->delete();
        return response()->json(['message' => 'Reader deleted successfully']);
    }

    public function showProfile()
    {
        $user = Auth::user();
        $readerId = $user->reader->id;
        $reader = Reader::where('id', $readerId)->first();
        $CountService = new \App\Services\CountService($readerId);
        $locale = app()->getLocale();
        $badges = DB::table('reader_badges')->join('badges', 'badges.id', '=', 'reader_badges.badge_id')
            ->where('reader_badges.reader_id', $readerId)->get()->map(function ($badge) use ($locale) {
                $title = json_decode($badge->title, true);
                return [
                    'title' => $title[$locale] ?? '',
                    'icon' => $badge->image
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'profile reader retuierned successfully',
            'data' => [
                'name' => $reader->first_name . ' ' . $reader->last_name,
                'picture' => $reader->picture,
                'nickname' => $reader->nickname,
                'bio' => $reader->bio,
                'quote' => $reader->quote,
                'books_number' => $CountService->countBooks(),
                'countries_number' => $CountService->countCountries(),
                'challenges_number' => $CountService->countChallenges(),
                'total_points' => $reader->total_points,
                'badges' => $badges,
            ]
        ]);
    }
    public function getAllProfiles(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $readers = Reader::select('first_name', 'last_name', 'picture', 'nickname', 'total_points');
        if ($search) {
            $readers->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('picture', 'like', "%{$search}%")->orWhere('nickname', 'like', "%{$search}%")->orWhere('total_points', 'like', "%{$search}%");
            });
        }
        $results = $readers->get();
        return response()->json([
            'success' => true,
            'message' => 'return profiles',
            'data' => $results,
        ]);
    }
}
