<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;

use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CategoryController extends Controller
{
    public function getCategories()
    {
        $categories = Category::select('id', 'name', 'icon')->get();
        $user = Auth::user();
        $readerId = $user->reader?->id;

        $categories = $categories->map(function ($category) use ($readerId) {
            $locale = app()->getLocale();
            $is_followed = DB::table('reader_categories')->where('reader_categories.reader_id', '=', $readerId)->where('reader_categories.category_id', '=', $category->id)
                ->exists();

            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name', $locale),
                'icon' => $category->icon,
                'is_followed' => $is_followed,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        $categories = Category::withcount('books')
            ->paginate(6)
            ->through(function ($category) {
                return [
                    'id' => $category->id,
                    'icon' =>  $category->icon,
                    'name' => $category->getTranslations('name'),
                    'number_of_books' => $category->books_count,
                ];
            });
        return response()->json($categories);
    }
    public function store(StoreCategoryRequest $request)
    {
        $user = Auth::user();

        // Upload icon image to Cloudinary
        $imageUpload = Cloudinary::uploadApi()->upload(
            $request->file('icon')->getRealPath(),
            ['folder' => 'reading-app/categories']
        );
        $imageUrl = $imageUpload['secure_url'];

        // Create category
        $category = Category::create([
            'name' => [
                'en' => $request->input('name.en'),
                'ar' => $request->input('name.ar'),
            ],
            'icon' => $imageUrl,
        ]);

        return response()->json(['message' => 'Category added successfully']);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $user = Auth::user();
        $category = Category::select('id', 'name', 'icon')->findOrFail($id);
         // Update name translations
        if ($request->has('name_en')) {
            $category->setTranslation('name', 'en', $request->input('name_en'));
        }
        if ($request->has('name_ar')) {
            $category->setTranslation('name', 'ar', $request->input('name_ar'));
        }
        // Upload icon
        if ($request->hasFile('icon')) {
            $imageUpload = Cloudinary::uploadApi()->upload(
                $request->file('icon')->getRealPath(),
                ['folder' => 'reading-app/categories']
            );
            $category->icon = $imageUpload['secure_url'];
        }
        $category->save();
        return response()->json([
            'success'=>true,
            'message'=>'Category Updated Successfuly.',
            'data'=>[
            'id' => $category->id,
            'name' => $category->getTranslations('name'),
            'icon' =>  $category->icon, ]
        ]);
    }

    public function followCategory($categoryId)
    {
        $reader = Auth::user()->reader;

        if (!$reader) {
            return response()->json(['message' => 'Reader not found.'], 404);
        }

        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $isFollowing = $reader->categories()
            ->where('category_id', $categoryId)
            ->exists();

        if ($isFollowing) {
            return response()->json(['message' => 'Already following this category.'], 200);
        }

        $reader->categories()->attach($categoryId);

        return response()->json(['message' => 'Category followed successfully.'], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $category = Category::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->getTranslations('name'),
                'icon' =>  $category->icon,
            ]
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
