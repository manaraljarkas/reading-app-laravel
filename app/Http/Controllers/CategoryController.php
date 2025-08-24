<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;

use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Book;
use App\Models\Category;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->paginate(5)
            ->through(function ($category) {
                return [
                    'id' => $category->id,
                    'icon' =>  $category->icon,
                    'name' => $category->getTranslation('name', 'en'),
                    'number_of_books' => $category->books_count,
                ];
            });
        return response()->json($categories);
    }
    public function store(StoreCategoryRequest $request)
    {
        ini_set('max_execution_time', 360);
        $user = Auth::user();
        $data = $request->validated();
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

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully',
            'data' => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        ini_set('max_execution_time', 360);
        $user = Auth::user();
        $validated = $request->validated();
        if (empty($validated)) {
            return response()->json([
                'message' => 'No update data provided.'
            ], 422);
        }
        $category = Category::findOrFail($id);

        if ($request->hasFile('icon')) {
            $validated['icon'] = Cloudinary::uploadApi()->upload(
                $request->file('icon')->getRealPath(),
                ['folder' => 'reading-app/categories']
            )['secure_url'];
        }
        $category->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Category Updated Successfuly.',
            'data' => [
                'id' => $category->id,
                'name' => $category->getTranslations('name'),
                'icon' =>  $category->icon,
            ]
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

    public function unfollowCategory($categoryId)
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

        if (!$isFollowing) {
            return response()->json(['message' => 'You are not following this category.'], 200);
        }

        $reader->categories()->detach($categoryId);

        return response()->json(['message' => 'Category unfollowed successfully.'], 200);
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

    public function searchCategories(Request $request)
    {
        $search = $request->input('search');
        $locale = app()->getLocale();
        $user = Auth::user();
        $readerId = $user->reader?->id;

        $query = Category::select('id', 'name', 'icon');

        if ($search) {
            $query->where("name->$locale", 'LIKE', "%{$search}%");
        }

        $categories = $query->get();

        $categories = $categories->map(function ($category) use ($readerId, $locale) {
            $is_followed = DB::table('reader_categories')
                ->where('reader_id', $readerId)
                ->where('category_id', $category->id)
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
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = Category::query();
        if ($search) {
            $query->where('name->en', 'LIKE', "%{$search}%");
        }
       $categories=$query->get()->map(function($category){
        return[
         'id'=>$category->id,
         'name'=>$category->getTranslation('name','en')
        ];
       });
        return response()->json(['categories'=>$categories]);
    }
}
