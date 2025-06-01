<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
              protected $fillable = ['name','longitude','latitude'

    ];
         public function authors()
    {
        return $this->hasMany(Author::class);
    }
}
