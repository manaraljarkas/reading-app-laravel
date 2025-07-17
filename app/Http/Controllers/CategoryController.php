<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;

use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
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
            $locale=app()->getLocale();
            $is_followed = DB::table('reader_categories')->
            where('reader_categories.reader_id', '=', $readerId)->
            where('reader_categories.category_id', '=', $category->id)
            ->exists();

            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name',$locale),
                'icon' => asset('storage/images/categories/' . $category->icon),
                'is_followed' => $is_followed,
            ];
        });

        return response()->json([
       'success'=>true,
       'data'=>$categories
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        $categories = Category::withcount('books')
            ->paginate(10)
            ->through(function ($category) {
                return [
                    'id' => $category->id,
                    'icon' => asset('storage/images/categories/' . $category->icon),
                    'name' => $category->getTranslations('name'),
                    'number_of_books' => $category->books_count,
                ];
            });
        return response()->json($categories);
    }
    public function store(StoreCategoryRequest $request)
    {
        $user = Auth::user();

        $imagePath = $request->file('icon')->store('images/ccategories', 'public');
        $category = Category::create([
            'name' => [
                'en' => $request->input('name.en'),
                'ar' => $request->input('name.ar'),
            ],
            'icon' => $imagePath ?? null,
        ]);

        return response()->json(['message' => 'category added successufly']);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $user = Auth::user();
        $category = Category::select('id', 'name', 'icon')->findOrFail($id);

        if ($request->has('name_en')) {
            $category->setTranslation('name', 'en', $request->input('name_en'));
        }
        if ($request->has('name_ar')) {
            $category->setTranslation('name', 'ar', $request->input('name_ar'));
        }

        if ($request->hasFile('icon')) {
            $imagepath = $request->file('icon')->store('images/categories', 'public');
            $category->icon = $imagepath;
        }
        $category->save();
        return response()->json([
            'id' => $category->id,
            'name' => $category->getTranslations('name'),
            'icon' => asset('storage/images/categories/' . $category->icon),
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
}
