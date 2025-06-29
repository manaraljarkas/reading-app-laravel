<?php

namespace App\Http\Controllers;

use App\Models\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategotyController extends Controller
{
    public function index()
    {
        $categories = Category::select('id', 'name', 'icon')->get();
        $readerId = Auth::id();

        $categories = $categories->map(function ($category) use ($readerId) {
            $is_followed = DB::table('reader_categories')->where('reader_categories.reader_id', '=', $readerId)->where('reader_categories.category_id', '=', $category->id)->exists();

            return [
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon,
                'is_followed' => $is_followed,
            ];
        });

        return response()->json($categories);
    }
    public function getCategories()
    {
        $user = Auth::user();
        $categories = Category::withcount('books')
            ->paginate(10)
            ->through(function ($category) {
                return [
                    'id' => $category->id,
                    'icon' => asset('storage/' . $category->icon),
                    'name' => $category->name,
                    'number_of_books' => $category->books_count,
                ];
            });
        return response()->json($categories);
    }
    public function addcategory(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'icon' => 'image|required',
        ]);
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

    public function editCategory(Request $request, $id)
    {
        $user = Auth::user();
        $category = Category::select('id', 'name', 'icon')->findOrFail($id);
        $validated = $request->validate([
            'name.en' => 'sometimes|string',
            'name.ar' => 'sometimes|string',
            'icon' => 'sometimes|image',
        ]);
        if ($request->has('name')) {
            $category->name = [
                'en' => $request->input('name')['en'] ?? $category->name['en'],
                'ar' => $request->input('name')['ar'] ?? $category->name['ar'],
            ];
        }

        if ($request->hasFile('icon')) {
            $imagepath = $request->file('icon')->store('images/categories', 'public');
            $category->icon = $imagepath;
        }
        $category->save();
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'icon' => asset('storage/' . $category->icon),
        ]);
    }
}
