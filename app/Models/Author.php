<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Author extends Model
{
    use SoftDeletes,HasTranslations;
    protected $translatable = ['name'];
    protected $fillable = ['name','image','country_id'];
    protected $casts = [
    'name' => 'array',
     ];
    public function books()
    {
        return $this->hasMany(Book::class);
    }
     public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
