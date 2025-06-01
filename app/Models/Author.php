<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
          'name',
          'number_of_books',
          'image',
          'country_id'
    ];
    public function books()
    {
        return $this->hasMany(Book::class);
    }

     public function countries()
    {
        return $this->belongsTo(Country::class);
    }


}
