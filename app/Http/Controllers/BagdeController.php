<?php

namespace App\Http\Controllers;

use \Exception;
use App\Http\Requests\StoreBadgeRequest;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
class BagdeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $badges = Badge::select('title', 'image', 'achievment')
            ->withcount('readers')
            ->paginate(10)
            ->through(function ($badge) {
                return [
                    'image' => $badge->image,
                    'title' => $badge->getTranslations('title'),
                    'description' => $badge->getTranslations('achievment'),
                    'number_of_earnes' => $badge->readers_count,
                ];
            });

        return response()->json($badges);
    }

    public function destroy($badgeId)
    {
        $user = Auth::user();
        $badge = Badge::find($badgeId);
        if (!$badge) {
            return response()->json(['message' => 'Badge not found'], 404);
        }
        $badge->delete();
        return response()->json(['message' => 'Badge deleted successfully']);
    }

    public function store(StoreBadgeRequest $request)
    {
        $user = Auth::user();

        // Upload image to Cloudinary
        $imageUpload = Cloudinary::uploadApi()->upload(
            $request->file('image')->getRealPath(),
            ['folder' => 'reading-app/badges']
        );
        $imageUrl = $imageUpload['secure_url'];
        $badges = Badge::create([
            'title' => [
                'en' => $request->input('title.en'),
                'ar' => $request->input('title.ar'),
            ],
            'achievment' => [
                'en' => $request->input('achievment.en'),
                'ar' => $request->input('achievment.ar'),
            ],
            'image' => $imageUrl,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $badge = Badge::findOrFail($id);

        if ($request->has('title_en')) {
            $badge->setTranslation('title', 'en', $request->input('title_en'));
        }
        if ($request->has('title_ar')) {
            $badge->setTranslation('title', 'ar', $request->input('title_ar'));
        }
        if ($request->has('achievment_en')) {
            $badge->setTranslation('achievment', 'en', $request->input('achievment_en'));
        }
        if ($request->has('achievment_ar')) {
            $badge->setTranslation('achievment', 'ar', $request->input('achievment_ar'));
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
            'title' => $badge->getTranslations('title'),
            'achievment' => $badge->getTranslations('achievment'),
            'image' => asset('storage/images/badges/' . $badge->image),
        ]);
    }
    public function show($id)
    {
        $badge = Badge::findOrFail($id);

    return response()->json([
        'title' => $badge->getTranslations('title'),
        'achievment' => $badge->getTranslations('achievment'),
        'image' => $badge->image,
    ]);
}
}
