<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','longitude','latitude'];
    protected $casts=[
    'name'=>'array'
    ];
    public function authors()
    {
        return $this->hasMany(Author::class);
    }
}
