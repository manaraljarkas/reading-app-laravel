<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeCategory extends Model
{
    protected $fillable = ['name'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}
