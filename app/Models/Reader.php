<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reader extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'points',
        'first_name',
        'last_name',
        'picture',
        'bio',
        'nickname',
        'quote',
        'user_id'];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
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
        return $this->belongsToMany(Book::class, 'reader_books')->withPivot([
                    'progress',
                    'status',
                    'is_favourite',
                    'is_challenged',
                    'rating'
                ]);
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'reader_challenges') ->
        withPivot(['progress', 'percentage']);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'reader_categories');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bookSuggestions()
    {
        return $this->hasMany(BookSuggestion::class);
    }
}
