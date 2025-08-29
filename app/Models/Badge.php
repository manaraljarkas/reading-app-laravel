<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Badge extends Model
{
    use SoftDeletes,HasTranslations ;
    protected $fillable = ['title', 'achievment', 'image', 'type'];
    public $translatable = ['title', 'achievment'];
    protected $casts = [
        'title' => 'array',
        'achievment' => 'array',
    ];
    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'reader_badges');
    }
}
