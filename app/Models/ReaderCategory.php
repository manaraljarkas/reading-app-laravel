<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReaderCategory extends Model
{
    use HasFactory;

    protected $fillable = ['reader_id', 'category_id'];

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
