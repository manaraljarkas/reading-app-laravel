<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use App\Models\Request;

class Reader extends Model
{
    protected $fillable = [
        'points',
        'first_name',
        'last_name',
        'picture',
        'bio',
        'nickname',
        'quote',
        'number_of_books',
        'number_of_challenges',
         'user_id'];

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'reader_badges');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function books()
    {
        return $this->belongsToMany(Book::class, 'reader_books');
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'reader_challenges');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'reader_categories');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
