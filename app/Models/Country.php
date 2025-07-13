<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use SoftDeletes,HasTranslations;
    public $translatable = ['name'];
    protected $fillable = ['name','code'];
    protected $casts=[
    'name'=>'array'
    ];
    public function authors()
    {
        return $this->hasMany(Author::class);
    }
}
