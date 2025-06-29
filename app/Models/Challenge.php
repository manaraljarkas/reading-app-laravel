<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;
    protected $fillable =[
        'title',
        'description',
        'points',
        'duration',
        'number_of_books',
        'size_category_id',
        'category_id'];

     protected $casts=['title'=>'array'
     ,'description'=>'array',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'challenge_books');
    }
    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'reader_challenges');
    }
    public function sizeCategory()
    {
        return $this->belongsTo(SizeCategory::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
