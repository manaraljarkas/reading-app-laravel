<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSizeCategoryRequest;
use App\Http\Requests\UpdateSizeCategoryRequest;
use App\Models\SizeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class SizeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $sizeCategories = SizeCategory::paginate(5);

        return response()->json([
            'message' => 'Size categories retrieved successfully.',
            'data' => $sizeCategories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSizeCategoryRequest $request): JsonResponse
    {
        $sizeCategory = SizeCategory::create([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'message' => 'Size category created successfully.',
            'data' => $sizeCategory,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $sizeCategory = SizeCategory::find($id);

        if (!$sizeCategory) {
            return response()->json([
                'message' => 'Size category not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Size category retrieved successfully.',
            'data' => $sizeCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSizeCategoryRequest $request, string $id): JsonResponse
    {
        $sizeCategory = SizeCategory::find($id);

        if (!$sizeCategory) {
            return response()->json([
                'message' => 'Size category not found.',
            ], 404);
        }

        $sizeCategory->update([
            'name' => $request->input('name'),
        ]);

        return response()->json([
            'message' => 'Size category updated successfully.',
            'data' => $sizeCategory->refresh(),
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $sizeCategory = SizeCategory::find($id);

        if (!$sizeCategory) {
            return response()->json([
                'message' => 'Size category not found.',
            ], 404);
        }

        $sizeCategory->delete();

        return response()->json([
            'message' => 'Size category deleted successfully.',
        ]);
    }
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = SizeCategory::query();
        if ($search) {
            $query->where('name->en', 'LIKE', "%{$search}%");
        }
        $sizeCategories = $query->get()->map(function ($sizecategory) {
            return [
                'id' => $sizecategory->id,
                'name' => $sizecategory->getTranslation('name', 'en'),
            ];
        });
        return response()->json(['size_categories' => $sizeCategories]);
    }
}
