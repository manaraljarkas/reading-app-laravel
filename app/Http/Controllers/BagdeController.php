<?php

namespace App\Http\Controllers;

use \Exception;
use App\Http\Requests\StoreBadgeRequest;
use App\Http\Resources\BadgeResource;
use App\Models\Badge;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BagdeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $badges = Badge::select('id', 'title', 'image', 'achievment')
            ->withcount('readers')
            ->paginate(5)
            ->through(function ($badge) {
                return [
                    'id' => $badge->id,
                    'image' => $badge->image,
                    'title' => $badge->getTranslation('title', 'en'),
                    'description' => $badge->getTranslation('achievment', 'en'),
                    'number_of_earners' => $badge->readers_count,
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
        $data = $request->validated();
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
        return response()->json([
            'success' => true,
            'message' => 'Badge created successfully.',
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateBadgeRequest $request, $id)
    {
        $user = Auth::user();
        $badge = Badge::findOrFail($id);
        $data = $request->validated();

        if (empty($data)) {
            return response()->json([
                'message' => 'No update data provided.'
            ], 422);
        }

        if (isset($data['title'])) {
            $badge->setTranslations('title', $data['title']);
        }
        if (isset($data['achievment'])) {
            $badge->setTranslations('achievment', $data['achievment']);
        }

        if ($request->hasFile('image')) {
            $imageUpload = Cloudinary::uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'reading-app/badges']
            );
            $badge->image = $imageUpload['secure_url'];
        }

        $badge->save();
        return response()->json([
            'success' => true,
            'message' => 'Badge updated successfully.',
            'data' => [
                'title' => $badge->getTranslations('title'),
                'achievment' => $badge->getTranslations('achievment'),
                'image' => $badge->image,
            ]
        ]);
    }
    public function show($id)
    {
        $badge = Badge::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Badge retrieved successfully.',
            'data' => [
                'title' => $badge->getTranslations('title'),
                'achievment' => $badge->getTranslations('achievment'),
                'image' => $badge->image,
            ]
        ]);
    }
}
