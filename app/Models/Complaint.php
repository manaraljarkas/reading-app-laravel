<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use SoftDeletes;
    protected $fillable = ['subject','description','reader_id'];

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
}
