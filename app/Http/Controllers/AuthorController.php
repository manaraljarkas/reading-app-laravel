<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthoreRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Country;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    public function getAuthors(Request $request)
    {
        $reader = Auth::user();
        $search = $request->input('search');
        $locale = app()->getLocale();

        $query = Author::withCount('books')->with('country');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'LIKE', "%{$search}%")
                    ->orWhere('name->ar', 'LIKE', "%{$search}%")
                    ->orWhereHas('country', function ($q2) use ($search) {
                        $q2->where('name->en', 'LIKE', "%{$search}%")
                            ->orWhere('name->ar', 'LIKE', "%{$search}%");
                    });
            });
        }

        $authors = $query->get();

        $authors = $authors->map(function ($author) use ($locale) {
            return [
                'id' => $author->id,
                'name' => $author->getTranslation('name', $locale),
                'country_name' => $author->country?->getTranslation('name', $locale),
                'image' => $author->image,
                'number_of_books' => $author->books_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $authors,
        ]);
    }


    public function index()
    {
        $user = Auth::user();
        $authors = Author::with('country')
            ->withCount('books')
            ->paginate(5)
            ->through(function ($author) {
                return [
                    'id' => $author->id,
                    'image' => $author->image,
                    'name' => $author->getTranslation('name', 'en'),
                    'country' => $author->country->getTranslation('name', 'en'),
                    'number_of_books' => $author->books_count,
                ];
            });
        return response()->json($authors);
    }

    public function store(StoreAuthoreRequest $request)
    {
        ini_set('max_execution_time', 360);
        $user = Auth::user();
        $data = $request->validated();
        $imageUrl = null;
        if ($request->hasFile('image')) {
            // Upload image to Cloudinary
            $imageUpload = Cloudinary::uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'reading-app/authors']
            );
            $imageUrl = $imageUpload['secure_url'];
        }
        $author = Author::create([
            'name' => [
                'en' => $request->input('name.en'),
                'ar' => $request->input('name.ar'),
            ],
            'image' => $imageUrl,
            'country_id' => $request->country_id,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Author created successfully.',
            'data' => $author
        ]);
    }

    public function destroy($AuthorId)
    {
        $user = Auth::user();
        $author = Author::find($AuthorId);
        if (!$author) {
            return response()->json(['message' => 'Author not found'], 404);
        }
        $author->delete();
        return response()->json(['message' => 'Author deleted successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, $id)
    {
        ini_set('max_execution_time', 360);
        $user = Auth::user();
        $validated = $request->validated();
        $author = Author::with('country')->findOrFail($id);

        if ($request->hasFile('image')) {
            $validated['image'] = Cloudinary::uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'reading-app/authors']
            )['secure_url'];
        }

        $author->update($validated);
        $author->refresh()->load('country');
        return response()->json([
            'success' => true,
            'message' => 'Author Updated successfully.',
            'data' => [
                'name' => $author->getTranslations('name'),
                'country' => $author->country->getTranslations('name'),
                'image' => $author->image ?? null,
            ]
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $author = Author::with('country')->findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Author profile fetched successfully.',
            'data' => [
                'id' => $author->id,
                'name' => $author->getTranslations('name'),
                'image' =>  $author->image,
                'country' => $author->country?->getTranslations('name'),
            ]
        ]);
    }

    public function searchAuthors(Request $request)
    {
        $search = $request->input('search');
        $locale = app()->getLocale();

        $query = Author::withCount('books')->with('country');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name->en', 'LIKE', "%{$search}%")
                    ->orWhere('name->ar', 'LIKE', "%{$search}%")
                    ->orWhereHas('country', function ($q2) use ($search) {
                        $q2->where('name->en', 'LIKE', "%{$search}%")
                            ->orWhere('name->ar', 'LIKE', "%{$search}%");
                    });
            });
        }

        $authors = $query->get();

        $Authors = $authors->map(function ($author) use ($locale) {
            return [
                'name' => $author->getTranslation('name', $locale),
                'id' => $author->id,
                'country_name' => $author->country?->getTranslation('name', $locale),
                'image' => $author->image,
                'number_of_books' => $author->books_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $Authors,
        ]);
    }
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = Author::query();
        if ($search) {
            $query->where('name->en', 'LIKE', "%{$search}%");
        }
        $authors = $query->get()->map(function ($author) {
            return [
                'id' => $author->id,
                'name' => $author->getTranslation('name', 'en'),
            ];
        });
        return response()->json(['authors' => $authors]);
    }

    public function SearchAuthorWithPagination(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = Author::withCount('books');
        if ($search) {
            $query->where('author->en', 'LIKE', "%{$search}%");
        }
        $authors = $query->paginate(5)->through(function ($author) {
            return [
                'id' => $author->id,
                'image' => $author->image,
                'name' => $author->getTranslation('name', 'en'),
                'country' => $author->country->getTranslation('name', 'en'),
                'number_of_books' => $author->books_count,
            ];
        });
        return response()->json([
            'authors' => $authors
        ]);
    }

}
