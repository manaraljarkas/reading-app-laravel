<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','icon'];

    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'reader_categories');
    }
    public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

}
