<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SizeCategory extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];
    protected $casts = ['name' => 'array'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }
}
