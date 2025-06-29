<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    public function index()
    {
        $reader = Auth::user();

        $authors = Author::withCount('books')->with('country')->get();

        $Authors = $authors->map(function ($author) {
            return [
                'name' => $author->name,
                'id' => $author->id,
                'country_name' => $author->country?->name,
                'image' => $author->image ? asset('storage/' . $author->image) : null,
                'number_of_books' => $author->books_count,
            ];
        });

        return response()->json($Authors);
    }

    public function getAuthors_D()
    {
        $user = Auth::user();
        $authors = Author::with('country')
            ->withCount('books')
            ->paginate(10)
            ->through(function ($author) {
                return [
                    'id' => $author->id,
                    'image' => $author->image ? asset('storage/' . $author->image) : null,
                    'name' => $author->name,
                    'country' => $author->country->name,
                    'number_of_books' => $author->books_count,
                ];
            });
        return response()->json($authors);
    }

    public function AddAuthor(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name.en' => 'required|string',
            'name.ar' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'country_name.en' => 'required|string',
            'country_name.ar' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/authors', 'public');
        }
        $country = Country::create([
            'name' => [
                'en' => $request->input('country_name.en'),
                'ar' => $request->input('country_name.ar'),
            ],
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);

        $author = Author::create([
            'name' => [
                'en' => $request->input('name.en'),
                'ar' => $request->input('name.ar'),
            ],
            'image' => $imagePath,
            'country_id' => $country->id,
        ]);
    }

    public function deleteAuthor($AuthorId)
    {
        $user = Auth::user();
        $author = Author::find($AuthorId);
        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }
        $author->delete();
        return response()->json(['message' => 'Author deleted successfully']);
    }
    
    public function editAuthor(Request $request, $id)
    {
        $user = Auth::user();
        $author = Author::select('id', 'name', 'image', 'country_id')->with('country')->findOrFail($id);

        $validated = $request->validate([
            'name.en' => 'sometimes|string',
            'name.ar' => 'sometimes|string',
            'image' => 'sometimes|image',
            'country_id' => 'sometimes|exists:countries,id',
        ]);
        if ($request->has('name')) {
            $author->name = [
                'en' => $request->input('name')['en'] ?? $author->name['en'],
                'ar' => $request->input('name')['ar'] ?? $author->name['ar'],
            ];
        }
        if ($request->hasFile('image')) {
            $imagepath = $request->file('image')->store('images/authors', 'public');
            $author->image = $imagepath;
        }
        if ($request->has('country_id')) {
            $author->country_id = $request->country_id;
        }
        $author->save();
        $author->load('country');
        return response()->json([
            'name' => $author->name,
            'image' => asset('storage/' . $author->image),
            'country' => $author->country->name,
        ]);
    }
}
