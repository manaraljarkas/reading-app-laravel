<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
             protected $fillable = [
            'title',
            'achievment',
            'image'
    ];
        public function readers()
    {
        return $this->belongsToMany(Reader::class, 'reader_badges');
    }

}
