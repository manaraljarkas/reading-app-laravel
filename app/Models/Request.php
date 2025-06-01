<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
          protected $fillable = [
            'subject',
            'description',
            'reader_id'

    ];
    
      public function readers()
    {
        return $this->belongsTo(Reader::class);
    }

}
