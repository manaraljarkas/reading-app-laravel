<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthoreRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
class AuthorController extends Controller
{
    public function getAuthors()
    {
        $reader = Auth::user();

        $authors = Author::withCount('books')->with('country')->get();

        $Authors = $authors->map(function ($author) {
            $locale=app()->getLocale();
            return [
                'name' => $author->getTranslation('name',$locale),
                'id' => $author->id,
                'country_name' => $author->country?->getTranslation('name',$locale),
                'image' => $author->image ,
                'number_of_books' => $author->books_count,
            ];
        });

        return response()->json([
        'success'=>true,
        'data'=>$Authors,
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $authors = Author::with('country')
            ->withCount('books')
            ->paginate(10)
            ->through(function ($author) {
                return [
                    'id' => $author->id,
                    'image' => $author->image ,
                    'name' => $author->getTranslations('name'),
                    'country' => $author->country->getTranslations('name'),
                    'number_of_books' => $author->books_count,
                ];
            });
        return response()->json($authors);
    }

    public function store(StoreAuthoreRequest $request)
    {
        $user = Auth::user();
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

    public function update(UpdateAuthorRequest $request, $id)
    {
        $user = Auth::user();
        $author = Author::select('id', 'name', 'image', 'country_id')->with('country')->findOrFail($id);


        // if ($request->has('name')) {
        //     $author->name = [
        //         'en' => $request->input('name')['en'] ?? $author->name['en'],
        //         'ar' => $request->input('name')['ar'] ?? $author->name['ar'],
        //     ];
        // }
        if ($request->has('name')) {
            $newTranslations = $request->input('name', []);
            $oldTranslations = $author->getTranslations('name');

            $author->setTranslations('name', array_merge($oldTranslations, $newTranslations));
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
            'name' => $author->getTranslations('name'),
            'image' => asset('storage/images/authors/' . $author->image),
            'country' => $author->country->getTranslations('name'),
        ]);
    }

    public function show($id){
        $user = Auth::user();
        $author = Author::with('country')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $author ->id,
                'name' => $author ->getTranslations('name'),
                'image' =>  $author->image,
                'country'=>$author->country?->getTranslations('name'),
            ]
        ]);
    }


}
