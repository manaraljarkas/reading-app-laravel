<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','image','country_id'];
    public function books()
    {
        return $this->hasMany(Book::class);
    }
     public function countries()
    {
        return $this->belongsTo(Country::class);
    }
}
