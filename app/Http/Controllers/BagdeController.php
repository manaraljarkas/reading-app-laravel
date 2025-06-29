<?php

namespace App\Http\Controllers;

use \Exception;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BagdeController extends Controller
{
    public function getBadges()
    {
        $user = Auth::user();
        $badges = Badge::select('title', 'image', 'achievment')
            ->withcount('readers')
            ->paginate(10)
            ->through(function ($badge) {
                return [
                    'image' => asset('storage/' . $badge->image),
                    'title' => $badge->title,
                    'description' => $badge->achievment,
                    'number_of_earnes' => $badge->readers_count,
                ];
            });

        return response()->json($badges);
    }
    public function deletebadge($badgeId)
    {
        $user = Auth::user();
        $badge = Badge::find($badgeId);
        if (!$badge) {
            return response()->json(['message' => 'Badge not found'], 404);
        }
        $badge->delete();
        return response()->json(['message' => 'Badge deleted successfully']);
    }
    public function addBadge(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'achievment.en' => 'required|string',
            'achievment.ar' => 'required|string',
            'image' => 'image|required',
        ]);
        $imagePath = $request->file('image')->store('images/badges', 'public');
        $badges = Badge::create([
            'title' => [
                'en' => $request->input('title.en'),
                'ar' => $request->input('title.ar'),
            ],
            'achievment' => [
                'en' => $request->input('achievment.en'),
                'ar' => $request->input('achievment.ar'),
            ],
            'image' => $imagePath,
        ]);
    }
    public function editBadge(Request $request, $id)
    {
        $user = Auth::user();
        $badge = Badge::findOrFail($id);
        $validate = $request->validate([
            'title.en' => 'sometimes|string',
            'title.ar' => 'sometimes|string',
            'achievment.en' => 'sometimes|string',
            'achievment.ar' => 'sometimes|string',
            'image' => 'sometimes|image',
        ]);

        if ($request->has('title')) {
            $badge->title = [
                'en' => $request->input('title')['en'] ?? $badge->title['en'],
                'ar' => $request->input('title')['ar'] ?? $badge->title['ar'],
            ];
        }
        if ($request->has('achievment')) {
            $badge->achievment = [
                'en' => $request->input('achievment')['en'] ?? $badge->achievment['en'],
                'ar' => $request->input('achievment')['ar'] ?? $badge->achievment['ar'],
            ];
        }

        if ($request->hasFile('image')) {
            $imagepath = $request->file('image')->store('images/badges', 'public');
            $badge->image = $imagepath;
        }
        try {
            $badge->save();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return response()->json([
            'title' => $badge->title,
            'achievment' => $badge->achievment,
            'image' => asset('storage/' . $badge->image),
        ]);
    }
}
