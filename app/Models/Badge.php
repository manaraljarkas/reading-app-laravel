<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Badge extends Model
{
    use SoftDeletes;
    protected $fillable = ['title','achievment','image'];
    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'reader_badges');
    }

}
