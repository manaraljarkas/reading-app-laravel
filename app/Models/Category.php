<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use SoftDeletes,HasTranslations;
    protected $fillable = ['name','icon'];
    public $translatable = ['name'];


    protected $casts=[
    'name'=>'array'
    ];
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
